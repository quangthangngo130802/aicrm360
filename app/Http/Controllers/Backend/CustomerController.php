<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Http\Requests\CustomerRequest;
use App\Models\Customer;
use App\Models\CustomerCategory;
use App\Models\Source;
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
        $title = 'Danh sách khách hàng';
        if ($request->ajax()) {
            $user = Auth::user();

            $query = $this->queryBuilder(
                model: new Customer,
                columns: ['*'],
            );

            if ($user->is_admin == 0) {
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

        return view('backend.customer.index', ['isAdmin' => Auth::user()->is_admin, 'title' => $title]);
    }

    public function save(?string $id = null)
    {

        $title          = "Tạo mới khách hàng";
        $customer       = null;

        $customerCategory = CustomerCategory::pluck('name', 'id')->toArray();
        $sources = Source::pluck('name', 'id')->toArray();
        if (!empty($id)) {
            $customer   = Customer::findOrFail($id);

            $title      = "Chỉnh sửa khách hàng - {$customer->name}";
        }

        return view('backend.customer.save', compact('title', 'customer', 'customerCategory', 'sources'));
    }

    public function store(CustomerRequest $request)
    {

        return transaction(function () use ($request) {

            $credentials = $request->validated();

            $credentials['code'] = $this->generateCustomerCode();

            $credentials['user_id'] = Auth::user()->id;

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
            $credentials['code'] ??= $this->generateCustomerCode();

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
        $maxAttempts = 5; // thử tối đa 5 lần
        $prefix = 'CRM';

        for ($i = 0; $i < $maxAttempts; $i++) {
            // Tìm mã code lớn nhất đang có
            $lastCode = Customer::query()
                ->where('code', 'like', $prefix . '%')
                ->orderByDesc(DB::raw('CAST(SUBSTRING(code, ' . (strlen($prefix) + 1) . ') AS UNSIGNED)'))
                ->value('code');

            if (!$lastCode) {
                $newCode = $prefix . '00001';
            } else {
                $number = (int) Str::after($lastCode, $prefix);
                $nextNumber = $number + 1;
                $newCode = $prefix . str_pad($nextNumber, 5, '0', STR_PAD_LEFT);
            }

            // Kiểm tra xem mã này đã tồn tại chưa
            $exists = Customer::where('code', $newCode)->exists();

            if (!$exists) {
                return $newCode;
            }

            // Nếu trùng thì thử lại (mã đã bị tạo bởi request khác)
        }

        throw new \Exception("Không thể tạo mã khách hàng duy nhất sau nhiều lần thử.");
    }
}
