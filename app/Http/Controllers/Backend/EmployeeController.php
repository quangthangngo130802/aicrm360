<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Http\Requests\EmployeeRequest;
use App\Models\ContractType;
use App\Models\Department;
use App\Models\EducationLevel;
use App\Models\Employee;
use App\Models\EmploymentStatus;
use App\Models\Position;
use App\Models\User;
use App\Traits\DataTables;
use App\Traits\QueryBuilder;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Permission;


class EmployeeController extends Controller
{
    use QueryBuilder;
    use DataTables;

    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = $this->queryBuilder(
                model: new User,
                columns: ['*'],

            )->where('is_admin', 0);

            return $this->processDataTable(
                $query,
                fn ($dataTable) =>
                $dataTable

                    ->editColumn('status', fn ($row) => view('components.switch-checkbox', ['checked' => $row->status, 'id' => $row->id])->render())
                    ->editColumn('operations', fn ($row) => view('components.operation', ['row' => $row])->render()),
                ['status', 'contract_code',]

            );
        }

        return view('backend.employee.index');
    }


    public function save(?string $id = null)
    {

        $title          = "Tạo mới nhân viên";
        $employee       = null;


        if (!empty($id)) {
            $employee   = User::findOrFail($id);

            $title      = "Chỉnh sửa nhân viên - {$employee->full_name}";
        }

        return view('backend.employee.save', compact('title', 'employee'));
    }

    public function store(EmployeeRequest $request)
    {

        return transaction(function () use ($request) {
            $credentials = $request->validated();
            $credentials['code'] ??= $this->generateEmployeeCode();
            $credentials['password'] = bcrypt($credentials['password']);

            if (!empty($credentials['birthday'])) {
                $credentials['birthday'] = Carbon::createFromFormat('d-m-Y', $credentials['birthday'])->format('Y-m-d');
            }

            User::create($credentials);

            return successResponse("Tạo nhân viên thành công", ['redirect' => '/employees']);
        });
    }

    public function update(EmployeeRequest $request, $id)
    {
        $employee = User::findOrFail($id);

        return transaction(function () use ($request, $employee) {
            $credentials = $request->validated();
            $credentials['code'] ??= $this->generateEmployeeCode();
            // dd($this->generateEmployeeCode());

            if (!empty($credentials['birthday'])) {
                $credentials['birthday'] = Carbon::createFromFormat('d-m-Y', $credentials['birthday'])->format('Y-m-d');
            }
            // Hash mật khẩu nếu có
            if (!empty($credentials['password'])) {
                $credentials['password'] = bcrypt($credentials['password']);
            } else {
                unset($credentials['password']);
            }

            // Cập nhật nhân viên
            $employee->update($credentials);
            return successResponse("Lưu thay đổi thành công", ['redirect' => '/employees']);
        });
    }

    private function generateEmployeeCode(): string
    {
        $lastCode = User::query()
            ->where('code', 'like', 'NS%')
            ->orderByDesc(DB::raw('CAST(SUBSTRING(code, 3) AS UNSIGNED)'))
            ->value('code');

        if (!$lastCode) {
            return 'NS00001';
        }

        // Lấy phần số phía sau mã
        $number = (int) Str::after($lastCode, 'NS');
        $nextNumber = $number + 1;

        // Luôn pad đến 5 chữ số
        return 'NS' . str_pad($nextNumber, 5, '0', STR_PAD_LEFT);
    }

}
