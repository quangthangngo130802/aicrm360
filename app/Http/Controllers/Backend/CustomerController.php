<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Http\Requests\CustomerRequest;
use App\Models\Customer;
use App\Models\CustomerCategory;
use App\Traits\DataTables;
use App\Traits\QueryBuilder;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CustomerController extends Controller
{
    use QueryBuilder;
    use DataTables;

    public function index(Request $request)
    {
        if ($request->ajax()) {
            $user = Auth::user();

            $query = $this->queryBuilder(
                model: new Customer,
                columns: ['*'],
            );

            if (!$user->is_admin) {
                $query->where('user_id', $user->id);
            }

            return $this->processDataTable(
                $query,
                fn ($dataTable) =>
                $dataTable
                    ->addColumn('username', fn ($row) => $row->user->name)
                    ->editColumn('operations', fn ($row) => view('components.operation', ['row' => $row])->render()),
                ['username']

            );
        }

        return view('backend.customer.index');
    }

    public function save(?string $id = null)
    {

        $title          = "Tạo mới khách hàng";
        $customer       = null;

        $customerCategory = CustomerCategory::pluck('name', 'id')->toArray();
        if (!empty($id)) {
            $customer   = Customer::findOrFail($id);

            $title      = "Chỉnh sửa khách hàng - {$customer->name}";
        }

        return view('backend.customer.save', compact('title', 'customer', 'customerCategory'));
    }

    public function store(CustomerRequest $request)
    {

        return transaction(function () use ($request) {

            $credentials = $request->validated();

            $credentials['code'] ??= $this->generateCustomerCode();

            if (!empty($credentials['birthday'])) {
                $credentials['birthday'] = Carbon::createFromFormat('d-m-Y', $credentials['birthday'])->format('Y-m-d');
            }

            Customer::create($credentials);

            return successResponse("Tạo nhân viên thành công", ['redirect' => '/customers']);
        });
    }

    public function update(CustomerRequest $request, $id)
    {
        // dd($request->all());
        $customer = Customer::findOrFail($id);

        return transaction(function () use ($request, $customer) {
            $credentials = $request->validated();
            $credentials['code'] ??= $this->generateEmployeeCode();

            if (!empty($credentials['birthday'])) {
                $credentials['birthday'] = Carbon::createFromFormat('d-m-Y', $credentials['birthday'])->format('Y-m-d');
            }

            // Cập nhật nhân viên
            $customer->update($credentials);
            return successResponse("Lưu thay đổi thành công", ['redirect' => '/customers']);
        });
    }

    private function generateCustomerCode(): string
    {
        $lastCode = Customer::query()
            ->where('code', 'like', 'KH%')
            ->orderByDesc(DB::raw('CAST(SUBSTRING(code, 3) AS UNSIGNED)'))
            ->value('code');

        if (!$lastCode) {
            return 'KH00001';
        }

        // Lấy phần số phía sau mã
        $number = (int) Str::after($lastCode, 'KH');
        $nextNumber = $number + 1;

        // Luôn pad đến 5 chữ số
        return 'NS' . str_pad($nextNumber, 5, '0', STR_PAD_LEFT);
    }
}
