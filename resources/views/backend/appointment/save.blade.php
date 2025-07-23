@extends('backend.layouts.app')


@section('content')
    @php
        $breadcrumbs = [['label' => 'Lịch hẹn', 'url' => '/appointment'], ['label' => 'Cập nhật lịch hẹn']];
    @endphp
    <x-breadcrumb :breadcrumbs="$breadcrumbs" />

    <x-page-header :title="$title" />

    <form action="" method="POST" enctype="multipart/form-data" id="myForm">
        @csrf

        @isset($appointment)
            @method('PUT')
        @endisset

        <div class="row">
            {{-- Cột trái: Thông tin nhân viên --}}
            <div class="col-md-9">
                <div class="card mb-4">
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <x-select label="Khách hàng" name="customer_id" :value="$appointment->customer_id ?? ''" :options="$customers" />
                            </div>

                            <div class="col-md-6">
                                <x-select label="Nhân viên phụ trách" name="user_id" :value="$appointment->user_id ?? ''" :options="$users" />

                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-medium">Ngày & Giờ</label>
                                <input type="datetime-local" class="form-control" style="padding: 8px" name="scheduled_at"
                                    value="{{ old('scheduled_at', isset($appointment) ? \Carbon\Carbon::parse($appointment->scheduled_at)->format('Y-m-d\TH:i') : '') }}">
                            </div>


                            <div class="col-md-6">
                                <x-select label="Status" name="status" :value="old('status', $appointment->status ?? 'Pending')" :options="[
                                    'pending' => 'Đang chờ',
                                    'completed' => 'Đã hoàn thành',
                                    'cancelled' => 'Đã hủy',
                                ]" />

                            </div>

                            <div class="col-12">
                                <label class="form-label">Ghi chú</label>
                                <textarea class="form-control" name="note" rows="3" placeholder="Nội dung chi tiết...">{{ $appointment->note ?? '' }}</textarea>
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


            submitForm("#myForm", function(response) {

                window.location.href = response.data.redirect
            });
        });
    </script>
@endpush
