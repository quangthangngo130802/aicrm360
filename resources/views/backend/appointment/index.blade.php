@extends('backend.layouts.app')

@section('content')
    <div class="py-4">

        <x-breadcrumb :breadcrumbs="[['label' => 'Quản lý Lịch Hẹn']]" />

        <x-page-header title="Quản lý Lịch Hẹn">
            @if (Auth::user()->is_admin == 1)
            <a href="#" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createAppointmentModal">
                <i class="fas fa-plus me-1"></i> Tạo lịch hẹn mới
            </a>
            @endif
        </x-page-header>

        <!-- Filter Section -->
        <div class="card mb-3">
            <div class="card-body">
                <div class="row g-2">
                    <div class="col-md-3">
                        <input type="text" class="form-control" id="search-customer"
                            placeholder="Tìm theo tên khách hàng...">
                    </div>
                    <div class="col-md-3">
                        <select class="form-select" id="search-status">
                            <option value="">Lọc theo trạng thái</option>
                            <option value="pending">Đang chờ</option>
                            <option value="completed">Đã hoàn thành</option>
                            <option value="cancelled">Đã huỷ</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <input type="date" class="form-control" id="search-date">
                    </div>
                    <div class="col-md-3">
                        <input type="text" class="form-control" id="search-general" placeholder="Tìm kiếm chung">
                    </div>
                </div>
            </div>
        </div>

        <!-- Table -->
        <div class="table-responsive">
            <table class="table table-bordered align-middle">
                <thead class="table-light">
                    <tr>
                        <th>STT</th>
                        <th>Khách hàng</th>
                        <th>Ghi chú</th>
                        <th>Nhân viên</th>
                        <th>Thời gian hẹn</th>
                        <th>Trạng thái</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody id="appointment-table">
                    @include('backend.appointment.partials.table')
                </tbody>
            </table>
        </div>

        <div id="pagination-links">
            @include('backend.appointment.partials.pagination')
        </div>


    </div>

    <!-- Modal: Tạo lịch hẹn mới -->
    <div class="modal fade" id="createAppointmentModal" tabindex="-1" aria-labelledby="createAppointmentModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <form action="{{ url('/apppointment/save') }}" method="POST" enctype="multipart/form-data" id="myForm">

                @csrf

                @isset($appointment)
                    @method('PUT')
                @endisset
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">
                            <img src="https://cdn-icons-png.flaticon.com/512/747/747310.png" width="20" class="me-2">
                            <span class="text-primary fw-bold">Tạo lịch hẹn mới</span>
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Đóng"></button>
                    </div>

                    <div class="modal-body">

                        <div class="row g-3">
                            <div class="col-md-6">
                                <x-select label="Khách hàng" name="customer_id" :value="$appointment->customer_id ?? ''" :options="$customers" />
                            </div>

                            <div class="col-md-6">
                                <x-select label="Nhân viên phụ trách" name="user_id" :value="$appointment->user_id ?? ''" :options="$users" />

                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-medium ">Ngày & Giờ</label>
                                <input type="datetime-local" class="form-control " style="padding: 8px" name="scheduled_at">
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
                                <textarea class="form-control" name="note" rows="3" placeholder="Nội dung chi tiết..."></textarea>
                            </div>
                        </div>

                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Huỷ</button>
                        <button type="submit" class="btn btn-success">
                            <i class="bi bi-save"></i> Lưu lịch hẹn
                        </button>
                    </div>
            </form>
        </div>

    </div>
@endsection

@push('styles')
    <style>
        .swal2-popup.small-swal {
            font-size: 0.8rem !important;
            width: 400px !important;
        }

        .badge-waiting {
            background-color: #ffc107 !important;
            color: black;
            padding: 5px 20px;
        }

        .badge-success {
            background-color: #198754 !important;
        }

        .badge-danger {
            background-color: #dc3545 !important;
            padding: 5px 26px;
        }
    </style>
@endpush

@push('scripts')
    <script>
        function fetchAppointments(url = "/apppointment") {
            let customer = $('#search-customer').val();
            let status = $('#search-status').val();
            let date = $('#search-date').val();
            let keyword = $('#search-general').val();

            $.ajax({
                url: url,
                type: 'GET',
                data: {
                    customer: customer,
                    status: status,
                    date: date,
                    keyword: keyword
                },
                dataType: 'json',
                success: function(response) {
                    $('#appointment-table').html(response.table);
                    $('#pagination-links').html(response.pagination);
                    history.pushState(null, '', url);
                },
                error: function() {
                    alert('Lỗi khi tải dữ liệu.');
                }
            });
        }

        // Gọi lại khi thay đổi filter
        $('#search-customer, #search-status, #search-date, #search-general').on('input change', function() {
            fetchAppointments(); // Mặc định gọi trang đầu
        });

        // Gọi lại khi phân trang
        $(document).on('click', '#pagination-links .pagination a', function(e) {
            e.preventDefault();
            let url = $(this).attr('href');
            if (url) {
                fetchAppointments(url);
            }
        });

        submitForm("#myForm", function(response) {
            $("#createAppointmentModal").modal("hide");
            fetchAppointments(); // Load lại bảng
            aicrm?.success("Tạo lịch hẹn thành công!");
        }, "/apppointment/save");
    </script>
    <script>
        $(document).on('click', '.btn-delete-appointment', function() {
            const id = $(this).data('id');

            Swal.fire({
                title: 'Xoá lịch hẹn?',
                text: "Bạn có chắc chắn muốn xoá lịch hẹn này?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Xoá',
                cancelButtonText: 'Không',
                customClass: {
                    popup: 'small-swal'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: `/apppointment/delete`,
                        type: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}',
                            id: id
                        },
                        success: function(res) {
                            Swal.fire('Đã xoá!', res.message || 'Lịch hẹn đã được xoá.',
                                    'success')
                                .then(() => location.reload());
                        },
                        error: function(xhr) {
                            Swal.fire('Lỗi!', xhr.responseJSON?.message ||
                                'Không thể xoá lịch hẹn.', 'error');
                        }
                    });
                }
            });
        });
    </script>
@endpush
