@extends('backend.layouts.app')

@section('content')
    <x-breadcrumb :breadcrumbs="[['label' => 'Đơn hàng']]" />

    <x-page-header title="Danh sách đơn hàng">
        <a href="/orders/save" class="btn btn-primary">
            <i class="fas fa-plus me-1"></i> Tạo mới đơn hàng
        </a>
    </x-page-header>

    <x-table fileName="orders" />
@endsection

@push('scripts')
    <script>
        $(function() {
            const api = "/orders"
            dataTables(api, {
                fixedColumns: {
                    left: 6,
                    right: 2
                },
            })

            handleDestroy('Order')

            initStatusToggle({
                model: 'Order'
            });

            initBulkAction('Order')
        })
    </script>
@endpush
