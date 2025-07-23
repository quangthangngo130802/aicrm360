<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Http\Requests\AppointmentRequest;
use App\Models\Appointment;
use App\Models\Customer;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AppointmentController extends Controller
{
    //
    public function index(Request $request)
    {

        $customers = Customer::pluck('name', 'id')->toArray();

        if(Auth::user()->is_admin == 0) {
            $customers = Customer::where('user_id', Auth::id())->pluck('name', 'id')->toArray();
        }


        $users = User::where('is_admin', 0)->pluck('name', 'id')->toArray();

        $query = Appointment::with(['customer', 'user'])->latest();

        if(Auth::user()->is_admin == 0) {
            $query->where('user_id', Auth::id());
        }

        if ($request->customer) {
            $query->whereHas('customer', function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->customer . '%');
            });
        }

        if ($request->status) {
            $query->where('status', $request->status);
        }

        if ($request->date) {
            $query->whereDate('scheduled_at', $request->date);
        }

        if ($request->keyword) {
            $query->where(function ($q) use ($request) {
                $q->where('note', 'like', '%' . $request->keyword . '%')
                    ->orWhereHas('customer', fn ($q) => $q->where('name', 'like', '%' . $request->keyword . '%'))
                    ->orWhereHas('user', fn ($q) => $q->where('name', 'like', '%' . $request->keyword . '%'));
            });
        }

        $appointments = $query->paginate(10);

        if ($request->ajax()) {
            return response()->json([
                'table' => view('backend.appointment.partials.table', compact('appointments'))->render(),
                'pagination' => view('backend.appointment.partials.pagination', compact('appointments'))->render()
            ]);
        }

        return view('backend.appointment.index', compact('appointments', 'customers', 'users'));
    }


    public function update(AppointmentRequest $request, $id)
    {
        dd($request->all());
    }

    public function store(AppointmentRequest $request)
    {
        return transaction(function () use ($request) {

            $credentials = $request->validated();

            Appointment::create($credentials);

            return successResponse("Tạo lịch hẹn thành công", ['redirect' => '/apppointment']);
        });

    }

    public function view($id){
        $appointment = Appointment::with(['customer', 'user'])->find($id);
        if(!$appointment){
            abort(404);
        }
        return view('backend.appointment.view', compact('appointment'));
    }

    public function updateStatus(Request $request){
        $request->validate([
            'id' => 'required|exists:appointments,id',
            'status' => 'required|in:pending,completed,cancelled'
        ]);

        $appointment = Appointment::find($request->id);

        $appointment->status = $request->status;

        $appointment->save();

        return successResponse("Cập nhật trạng thái thành công", ['redirect' => '/apppointment']);
    }

    public function delete(Request $request){
        // dd($request->all());
        $appointment = Appointment::find($request->id);
        if (!$appointment) {
            return errorResponse("Lịch hẹn không tồn tại", 404);
        }

        $appointment->delete();

        return successResponse("Xóa lịch hẹn thành công", ['redirect' => '/apppointment']);
    }
}
