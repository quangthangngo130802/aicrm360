@extends('backend.layouts.app')

@section('content')
    <x-breadcrumb :breadcrumbs="[['label' => 'Khách hàng']]" />

    <x-page-header title="Danh sách khách hàng">
        @if (Auth::user()->is_admin != 1)
            <a href="/customers/save" class="btn btn-primary">
                <i class="fas fa-plus me-1"></i> Tạo mới khách hàng
            </a>
        @endif
    </x-page-header>

    <x-table fileName="customer" />
@endsection

@push('scripts')
    <script>
        const isAdmin = @json($isAdmin);
        $(function() {
            const api = "/customers"
            dataTables(api, {
                hasCheckbox: !isAdmin,
                isOperation: !isAdmin,
            })

            handleDestroy('Customer')

            initStatusToggle({
                model: 'Customer'
            });

            initBulkAction('Customer')
        })
    </script>
@endpush
