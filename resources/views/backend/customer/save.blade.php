@extends('backend.layouts.app')


@section('content')
    @php
        $breadcrumbs = [
            ['label' => 'Khách hàng', 'url' => '/customer'],
            ['label' => $customer ? "Cập nhật khách hàng - $customer->full_name" : 'Tạo mới khách hàng'],
        ];
    @endphp
    <x-breadcrumb :breadcrumbs="$breadcrumbs" />

    <x-page-header :title="$title" />

    <form action="" method="POST" enctype="multipart/form-data" id="myForm">
        @csrf

        @isset($customer)
            @method('PUT')
        @endisset

        <div class="row">
            {{-- Cột trái: Thông tin nhân viên --}}
            <div class="col-md-9">
                <div class="card mb-4" id="companyDetailSection" class="d-none">
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <x-input name="code" id="code" label="Mã khách hàng"
                                    value="{{ $customer->code ?? '' }}" />
                            </div>

                            <div class="col-md-6">
                                <x-input name="name" id="name" label="Họ tên" required="true"
                                    value="{{ $customer->name ?? '' }}" />
                            </div>

                            <div class="col-md-6">
                                <x-input type="email" name="email" id="email" label="Email" required="true"
                                    value="{{ $customer->email ?? '' }}" />
                            </div>


                            <div class="col-md-6">
                                <x-input name="phone" id="phone" label="Số điện thoại" required="true"
                                    value="{{ $customer->phone ?? '' }}" />
                            </div>

                            <div class="col-md-6">
                                <x-input name="area" id="area" label="Địa chỉ"
                                    value="{{ $customer->area ?? '' }}" />
                            </div>

                            <div class="col-md-6">
                                <x-date name="birthday" id="birthday" label="Ngày sinh" :value="$customer && $customer->birthday
                                    ? \Illuminate\Support\Carbon::parse($customer->birthday)->format('d-m-Y')
                                    : ''" />
                            </div>

                            <div class="col-md-6">
                                <x-select label="Giới tính" name="gender" :value="$customer->gender ?? ''" :options="['male' => 'Nam', 'female' => 'Nữ', 'other' => 'Khác']" />
                            </div>

                            <div class="col-md-6">
                                <x-select label="Loại khách hàng" name="customer_category_id" :value="$customer->customer_category_id ?? ''" :options="$customerCategory" />
                            </div>

                            <div class="col-md-6">
                                <x-input name="demand" id="demand" label="Nhu cầu"
                                    value="{{ $customer->demand ?? '' }}" />
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card mb-4">

                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <x-input name="company_name" id="company_name" label="Tên công ty"
                                    value="{{ $customer->company_name ?? '' }}" />
                            </div>

                            <div class="col-md-6">
                                <x-input name="company_phone" id="company_phone" label="Điện thoại công ty"
                                    value="{{ $customer->company_phone ?? '' }}" />
                            </div>

                            <div class="col-md-6">
                                <x-input name="company_tax_code" id="company_tax_code" label="Mã số thuế"
                                    value="{{ $customer->company_tax_code ?? '' }}" />
                            </div>


                            <div class="col-md-6">
                                <x-input name="facebook_link" id="facebook_link" label="Link Facebook"
                                    value="{{ $customer->facebook_link ?? '' }}" />
                            </div>

                            <div class="col-md-6">
                                <x-input name="youtube_link" id="youtube_link" label="Link Youtube"
                                    value="{{ $customer->youtube_link ?? '' }}" />
                            </div>
                            <div class="col-md-6">
                                <x-input name="instagram_link" id="instagram_link" label="Link Instagram"
                                    value="{{ $customer->instagram_link ?? '' }}" />
                            </div>

                        </div>
                    </div>


                </div>


                <div class="text-center">
                    <button type="button" class="btn btn-primary btn-sm fs-6" id="submitRequestBtn">
                        Chi tiết
                    </button>
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
        $(function() {
            $(document).on('click', '.toggle-password', function() {
                const input = $($(this).attr('toggle'));
                console.log(input);

                const type = input.attr('type') === 'password' ? 'text' : 'password';
                input.attr('type', type);

                // Thay đổi icon
                $(this).html(type === 'password' ? '<i class="far fa-eye"></i>' :
                    '<i class="far fa-eye-slash"></i>');
            });

            submitForm("#myForm", function(response) {

                window.location.href = response.data.redirect
            });

        })
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const btn = document.getElementById('submitRequestBtn');
            const detailSection = document.getElementById('companyDetailSection');

            // Kiểm tra trạng thái từ localStorage
            const isVisible = localStorage.getItem('companyDetailVisible') === 'true';

            if (isVisible) {
                detailSection.classList.remove('d-none');
                btn.innerText = 'Quay lại';
            } else {
                detailSection.classList.add('d-none');
                btn.innerText = 'Chi tiết';
            }

            // Sự kiện khi bấm nút
            btn.addEventListener('click', function() {
                const currentlyVisible = !detailSection.classList.contains('d-none');

                if (currentlyVisible) {
                    detailSection.classList.add('d-none');
                    btn.innerText = 'Chi tiết';
                    localStorage.setItem('companyDetailVisible', 'false');
                } else {
                    detailSection.classList.remove('d-none');
                    btn.innerText = 'Quay lại';
                    localStorage.setItem('companyDetailVisible', 'true');
                }
            });
        });
    </script>
@endpush
