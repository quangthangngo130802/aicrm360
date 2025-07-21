@extends('backend.layouts.app')

@section('content')
    <x-breadcrumb :breadcrumbs="[['label' => 'Khách hàng']]" />

    <x-page-header title="Danh sách khách hàng">
        <a href="/customers/save" class="btn btn-primary">
            <i class="fas fa-plus me-1"></i> Tạo mới khách hàng
        </a>
    </x-page-header>

    <x-table fileName="customer" />
@endsection

@push('scripts')
    <script>
        $(function() {
            const api = "/customers"
            dataTables(api, {
                fixedColumns: {
                    left: 6,
                    right: 2
                },
            })

            handleDestroy('Customer')

            initStatusToggle({
                model: 'Customer'
            });

            initBulkAction('Customer')
        })
    </script>
@endpush
