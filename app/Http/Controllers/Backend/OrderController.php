<?php

namespace App\Http\Controllers\Backend;

use App\Enums\OrderStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\OrderRequest;
use App\Models\Customer;
use App\Models\Order;
use App\Traits\DataTables;
use App\Traits\QueryBuilder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class OrderController extends Controller
{
    use QueryBuilder;
    use DataTables;
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $user = Auth::user();
            $query = $this->queryBuilder(
                model: new Order(),
                columns: ['*'],
            );
            if ($user->is_admin != 1) {
                $query->where('user_id', $user->id);
            }

            return $this->processDataTable(
                $query,
                fn ($dataTable) =>
                $dataTable
                    ->editColumn(
                        'status',
                        fn ($row) =>
                        view('components.status-order', [
                            'status' => $row->status,
                        ])->render()
                    )
                    ->addColumn('username', fn ($row) => $row->user->name)
                    ->editColumn('total_amount', function ($row) {
                        return number_format($row->total_amount, 0, ',', '.') . ' ₫';
                    })
                    ->addColumn('customer', fn ($row) => $row->customer->name)
                    ->editColumn(
                        'operations',
                        fn ($row) =>
                        view('components.operation', ['row' => $row])->render()
                    ),
                ['status', 'username', 'customer', 'operations']
            );
        }
        $title = 'Quản lý đơn hàng';
        return view('backend.order.index', ['isAdmin' => Auth::user()->is_admin, 'title' => $title]);
    }


    public function save(?string $id = null)
    {

        $title          = "Tạo mới đơn hàng";
        $order       = null;
        $user = Auth::user();
        if ($user->is_admin == 1) {
            $customers = Customer::pluck('name', 'id')->toArray();
        } else {
            $customers = Customer::where('user_id', $user->id)->pluck('name', 'id')->toArray();
        }
        $orderStatuses = collect(OrderStatus::cases())->mapWithKeys(fn ($status) => [
            $status->value => $status->label()
        ])->toArray();


        if (!empty($id)) {
            $order   = Order::findOrFail($id);

            $title      = "Chỉnh sửa đơn hàng- {$order->code}";
        }

        return view('backend.order.save', compact('title', 'order', 'customers', 'orderStatuses'));
    }


    public function store(OrderRequest $request)
    {
        return transaction(function () use ($request) {
            // Chuyển "54.000.000" => "54000000"
            $totalAmount = str_replace('.', '', $request->input('total_amount'));

            // Chuẩn bị dữ liệu
            $data = $request->validated();
            $data['total_amount'] = $totalAmount;
            $data['user_id'] = Auth::id();
            $data['code'] ??= $this->generateOrderCode();

            // Tạo đơn hàng
            Order::create($data);

            return successResponse("Tạo đơn hàng thành công", ['redirect' => '/orders']);
        });
    }

    public function update(OrderRequest $request, $id)
    {
        return transaction(function () use ($request, $id) {
            $order = Order::findOrFail($id);


            $totalAmount = str_replace('.', '', $request->input('total_amount'));


            $data = $request->validated();
            $data['total_amount'] = $totalAmount;
            $data['code'] ??= $order->code;

            $order->update($data);

            return successResponse("Cập nhật đơn hàng thành công", ['redirect' => '/orders']);
        });
    }






    private function generateOrderCode(): string
    {
        $lastCode = Order::query()
            ->where('code', 'like', 'HD%')
            ->orderByDesc(DB::raw('CAST(SUBSTRING(code, 3) AS UNSIGNED)'))
            ->value('code');

        if (!$lastCode) {
            return 'HD00001';
        }

        // Lấy phần số phía sau mã
        $number = (int) Str::after($lastCode, 'HD');
        $nextNumber = $number + 1;

        // Luôn pad đến 5 chữ số
        return 'HD' . str_pad($nextNumber, 5, '0', STR_PAD_LEFT);
    }
}
