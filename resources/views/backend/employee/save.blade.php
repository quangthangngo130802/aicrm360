@extends('backend.layouts.app')


@section('content')
    @php
        $breadcrumbs = [
            ['label' => 'Nhân viên', 'url' => '/employees'],
            ['label' => $employee ? "Cập nhật nhân viên - $employee->full_name" : 'Tạo mới nhân viên'],
        ];
    @endphp
    <x-breadcrumb :breadcrumbs="$breadcrumbs" />

    <x-page-header :title="$title" />

    <form action="" method="POST" enctype="multipart/form-data" id="myForm">
        @csrf

        @isset($employee)
            @method('PUT')
        @endisset

        <div class="row">
            {{-- Cột trái: Thông tin nhân viên --}}
            <div class="col-md-9">
                <div class="card mb-4">
                    <div class="card-body">
                        <div class="row g-3">
                            {{-- <div class="col-md-6">
                                <x-input name="code" id="code" label="Mã nhân viên"
                                    value="{{ $employee->code ?? '' }}" />
                            </div> --}}

                            <div class="col-md-6">
                                <x-input name="name" id="name" label="Họ tên" required="true"
                                    value="{{ $employee->name ?? '' }}" />
                            </div>

                            <div class="col-md-6">
                                <x-input type="email" name="email" id="email" label="Email" required="true"
                                    value="{{ $employee->email ?? '' }}" />
                            </div>

                            <div class="col-md-6">
                                <x-input type="password" required="true" name="password" id="password"
                                    label="Mật khẩu {{ $employee ? 'mới (bỏ qua nếu không đổi)' : '' }}"
                                    placeholder="Mật khẩu" />
                            </div>

                            <div class="col-md-6">
                                <x-input name="phone" id="phone" label="Số điện thoại"
                                    value="{{ $employee->phone ?? '' }}" />
                            </div>

                            <div class="col-md-6">
                                <x-input name="address" id="address" label="Địa chỉ"
                                    value="{{ $employee->address ?? '' }}" />
                            </div>

                            <div class="col-md-6">
                                <x-date name="birthday" id="birthday" label="Ngày sinh" :value="$employee && $employee->birthday ? \Illuminate\Support\Carbon::parse($employee->birthday)->format('d-m-Y') : ''" />
                            </div>

                            <div class="col-md-6">
                                <x-select label="Giới tính" name="gender" :value="$employee->gender ?? ''" :options="['male' => 'Nam', 'female' => 'Nữ', 'other' => 'Khác']" />
                            </div>

                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-3">

                <x-submit />
                <x-card title="Quyền">
                    <x-select name="is_admin" :value="$employee->is_admin ?? ''" :options="['1' => 'Admin', '0' => 'Nhân viên']" />
                </x-card>
                <x-card title="Trạng thái">
                    <x-switch-checkbox :checked="$employee->status ?? true" />
                </x-card>

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
@endpush
