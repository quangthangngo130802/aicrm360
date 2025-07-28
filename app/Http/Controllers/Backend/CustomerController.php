<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Http\Requests\CustomerRequest;
use App\Models\Customer;
use App\Models\CustomerCategory;
use App\Models\Source;
use App\Models\User;
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
            $query = $this->queryBuilder(
                model: new Customer,
                columns: ['*'],
            );

            // Lọc theo quyền người dùng
            if (is_staff()) {
                $query->where('user_id', current_user()->id);
            } else {
                // Nếu là quản lý: lấy cả khách hàng của mình và nhân viên dưới quyền (nếu muốn)
                $query->where(function ($q) {
                    $q->where('user_id', current_user()->id)
                        ->orWhereHas('user', fn ($q2) => $q2->where('parent_id', current_user()->id));
                });
            }

            return $this->processDataTable(
                $query,
                fn ($dataTable) =>
                $dataTable
                    ->addColumn('username', fn ($row) => $row->user->name ?? '---')
                    ->editColumn('operations', fn ($row) => view('components.operation', ['row' => $row])->render()),
                ['username']
            );
        }

        return view('backend.customer.index', [
            'isAdmin' => current_user()->is_admin,
            'title'   => $title
        ]);
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

    // private function generateCustomerCode(): string
    // {
    //     $maxAttempts = 5; // thử tối đa 5 lần
    //     $prefix = 'KH';

    //     for ($i = 0; $i < $maxAttempts; $i++) {
    //         // Tìm mã code lớn nhất đang có
    //         $lastCode = Customer::query()
    //             ->where('code', 'like', $prefix . '%')
    //             ->orderByDesc(DB::raw('CAST(SUBSTRING(code, ' . (strlen($prefix) + 1) . ') AS UNSIGNED)'))
    //             ->value('code');

    //         if (!$lastCode) {
    //             $newCode = $prefix . '00001';
    //         } else {
    //             $number = (int) Str::after($lastCode, $prefix);
    //             $nextNumber = $number + 1;
    //             $newCode = $prefix . str_pad($nextNumber, 5, '0', STR_PAD_LEFT);
    //         }

    //         // Kiểm tra xem mã này đã tồn tại chưa
    //         $exists = Customer::where('code', $newCode)->exists();

    //         if (!$exists) {
    //             return $newCode;
    //         }

    //         // Nếu trùng thì thử lại (mã đã bị tạo bởi request khác)
    //     }

    //     throw new \Exception("Không thể tạo mã khách hàng duy nhất sau nhiều lần thử.");
    // }

    private function generateCustomerCode(): string
    {
        $maxAttempts = 5;
        $prefix = 'KH';

        // Lấy subdomain từ user
        $subdomain = User::where('id', Auth::user()->id)->value('subdomain');

        if (!$subdomain) {
            throw new \Exception("Không xác định được subdomain của người dùng.");
        }

        for ($i = 0; $i < $maxAttempts; $i++) {
            // Tìm mã code lớn nhất của khách hàng thuộc subdomain này (qua user)
            $lastCode = Customer::query()
                ->whereHas('user', fn ($q) => $q->where('subdomain', $subdomain))
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

            // Kiểm tra xem mã này đã tồn tại trong cùng subdomain chưa
            $exists = Customer::where('code', $newCode)
                ->whereHas('user', fn ($q) => $q->where('subdomain', $subdomain))
                ->exists();

            if (!$exists) {
                return $newCode;
            }
        }

        throw new \Exception("Không thể tạo mã khách hàng duy nhất sau nhiều lần thử.");
    }
}
