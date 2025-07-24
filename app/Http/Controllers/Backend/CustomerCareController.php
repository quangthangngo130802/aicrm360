<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Http\Requests\CustomerCareRequest;
use App\Models\Channel;
use App\Models\Customer;
use App\Models\CustomerCare;
use App\Models\Result;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CustomerCareController extends Controller
{
    public function index(Request $request)
    {
        $customers = Customer::get();
        $results = Result::get();
        if (Auth::user()->is_admin == 0) {
            $customers = Customer::where('user_id', Auth::id())->get();
        }

        $users = User::where('is_admin', 0)->get();
        if (Auth::user()->is_admin == 0) {
            $users = User::where('is_admin', 0)->where('id', Auth::id())->get();
        }

        // Subquery lấy ID lịch chăm sóc mới nhất theo mỗi khách hàng
        $latestCustomerCareIds = CustomerCare::selectRaw('MAX(id) as id')
            ->groupBy('customer_id');

        // Áp dụng main query với các bộ lọc
        $query = CustomerCare::with(['customer', 'user'])
            ->whereIn('id', $latestCustomerCareIds) // chỉ lấy lịch mới nhất mỗi khách
            ->latest();

        if (Auth::user()->is_admin == 0) {
            $query->where('user_id', Auth::id());
        }

        if ($request->customer) {
            $query->whereHas('customer', function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->customer . '%');
            });
        }

        if ($request->status) {
            $query->where('result_id', $request->status);
        }

        if ($request->user) {
            $query->where('user_id', $request->user);
        }

        if ($request->date) {
            $query->whereDate('care_date', $request->date);
        }

        if ($request->keyword) {
            $query->where(function ($q) use ($request) {
                $q->where('note', 'like', '%' . $request->keyword . '%')
                    ->orWhereHas('customer', fn ($q) => $q->where('name', 'like', '%' . $request->keyword . '%'))
                    ->orWhereHas('user', fn ($q) => $q->where('name', 'like', '%' . $request->keyword . '%'));
            });
        }

        $customer_cares = $query->paginate(10);

        if ($request->ajax()) {
            return response()->json([
                'table' => view('backend.customer_care.partials.table', compact('customer_cares'))->render(),
                'pagination' => view('backend.customer_care.partials.pagination', compact('customer_cares'))->render()
            ]);
        }
        $title = 'Danh sách nhật ký chăm sóc khách hàng';

        return view('backend.customer_care.index', compact('customer_cares', 'customers', 'users', 'results', 'title'));
    }


    public function save(?string $id = null)
    {
        $title      = "Thêm nhật ký nhật ký";
        $customers = Customer::pluck('name', 'id')->toArray();
        $results = Result::pluck('name', 'id')->toArray();
        $channels = Channel::pluck('name', 'id')->toArray();
        $customerCare = null;
        $users = User::where('is_admin', 0)->pluck('name', 'id')->toArray();
        if (Auth::user()->is_admin == 0) {
            $customers = Customer::where('user_id', Auth::id())->pluck('name', 'id')->toArray();
            $users = User::where('id', Auth::id())->pluck('name', 'id')->toArray();
        }



        if (!empty($id)) {
            $customerCare   = CustomerCare::findOrFail($id);
            $title      = "Chỉnh sửa nhật ký";
        }


        return view('backend.customer_care.save', compact('title', 'customerCare', 'customers', 'users','results','channels'));
    }

    public function update(CustomerCareRequest $request, $id)
    {
        return transaction(function () use ($request, $id) {
            $customerCare = CustomerCare::findOrFail($id);

            $data = $request->validated();


            $customerCare->update($data);

            return successResponse("Cập nhật nhật ký thành công", ['redirect' => '/customer_care']);
        });
    }

    public function store(CustomerCareRequest $request)
    {
        return transaction(function () use ($request) {

            $credentials = $request->validated();

            CustomerCare::create($credentials);

            return successResponse("Tạo lịch nhật ký thành công", ['redirect' => '/customer_care']);
        });
    }

    public function view($id)
    {
        $results = Result::get();
        $customerCare = CustomerCare::with(['customer', 'user'])->find($id);
        if (!$customerCare) {
            abort(404);
        }
        $customerCares = CustomerCare::where('customer_id', $customerCare->customer_id)
            ->orderBy('care_date', 'desc')
            ->get();
            $title = 'Thông tin nhật ký khách '.$customerCare->customer->name;
        return view('backend.customer_care.view', compact('customerCare', 'results', 'customerCares', 'title'));
    }

    // public function updateStatus(Request $request)
    // {
    //     $request->validate([
    //         'id' => 'required|exists:appointments,id',
    //         'status' => 'required|in:pending,completed,cancelled'
    //     ]);

    //     $appointment = Appointment::find($request->id);

    //     $appointment->status = $request->status;

    //     $appointment->save();

    //     return successResponse("Cập nhật trạng thái thành công", ['redirect' => '/apppointment']);
    // }

    public function delete(Request $request)
    {
        // dd($request->all());
        $appointment = CustomerCare::find($request->id);
        if (!$appointment) {
            return errorResponse("Lịch hẹn không tồn tại", 404);
        }

        $appointment->delete();

        return successResponse("Xóa lịch hẹn thành công", ['redirect' => '/apppointment']);
    }

    // public function editData($id)
    // {
    //     $appointment = Appointment::with(['customer', 'user'])->findOrFail($id);

    //     return response()->json($appointment);
    // }
}
