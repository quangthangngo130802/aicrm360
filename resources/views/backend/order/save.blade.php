@extends('backend.layouts.app')


@section('content')
    @php
        $breadcrumbs = [
            ['label' => 'Đơn hàng', 'url' => '/orders'],
            ['label' => $order ? 'Cập nhật đơn hàng' : 'Tạo mới đơn hàng'],
        ];
    @endphp
    <x-breadcrumb :breadcrumbs="$breadcrumbs" />

    <x-page-header :title="$title" />

    <form action="" method="POST" enctype="multipart/form-data" id="myForm">
        @csrf

        @isset($order)
            @method('PUT')
        @endisset

        <div class="row">
            {{-- Cột trái: Thông tin nhân viên --}}
            <div class="col-md-9">
                <div class="card mb-4">
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <x-input name="code" id="code" label="Mã đơn hàng"
                                    value="{{ $order->code ?? '' }}" />
                            </div>

                            <div class="col-md-6">
                                <x-select label="Khách hàng" name="customer_id" :value="$order->customer_id ?? ''" :options="$customers" />
                            </div>

                            <div class="col-md-6">
                                <x-input type="text" name="total_amount" id="total_amount" label="Tổng đơn hàng"
                                    required="true" value="{{ $order->total_amount ?? '' }}" />
                            </div>

                            <div class="col-md-6">
                                <x-select label="Trạng thái đơn hàng" name="status" :value="$order->status?->value ?? ''" :options="$orderStatuses" />
                            </div>


                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-3">

                <x-submit />

            </div>
        </div>
    </form>

    <x-media-popup />
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            const currencyFormatter = new Intl.NumberFormat('vi-VN');

            // Format ban đầu nếu có giá trị
            let initial = $('#total_amount').val().replace(/\D/g, '');
            if (initial) {
                $('#total_amount').val(currencyFormatter.format(initial));
            }

            // Format khi nhập
            $('#total_amount').on('input', function() {
                let raw = $(this).val().replace(/\D/g, '');
                $(this).val(currencyFormatter.format(raw));
            });

            $('#total_amount').on('keypress', function(e) {
                if (e.which < 48 || e.which > 57) e.preventDefault();
            });

            $('#total_amount').on('paste', function(e) {
                let text = e.originalEvent.clipboardData.getData('text');
                if (!/^\d+$/.test(text.replace(/\D/g, ''))) e.preventDefault();
            });


            submitForm("#myForm", function(response) {

                window.location.href = response.data.redirect
            });
        });
    </script>
@endpush
