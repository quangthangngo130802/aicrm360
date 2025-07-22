@extends('backend.layouts.app')

@section('content')
    <x-breadcrumb :breadcrumbs="[['label' => 'Đơn hàng']]" />

    <x-page-header title="Danh sách đơn hàng">
        @if (Auth::user()->is_admin != 1)
            <a href="/orders/save" class="btn btn-primary">
                <i class="fas fa-plus me-1"></i> Tạo mới đơn hàng
            </a>
        @endif
    </x-page-header>

    <x-table fileName="orders" />
@endsection

@push('scripts')
    <script>
        const isAdmin = @json($isAdmin);
        $(function() {
            const api = "/orders"
            dataTables(api, {
                hasCheckbox: !isAdmin,
                isOperation: !isAdmin,
                // fixedColumns: {
                //     left: 6,
                //     right: 2
                // },
            })

            handleDestroy('Order')

            initStatusToggle({
                model: 'Order'
            });

            initBulkAction('Order')
        })
    </script>
@endpush
