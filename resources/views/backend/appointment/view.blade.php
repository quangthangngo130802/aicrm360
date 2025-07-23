@extends('backend.layouts.app')

@section('content')
    <div class="py-4">
        <x-breadcrumb :breadcrumbs="[['label' => 'Chi tiết lịch hẹn']]" />

        <x-page-header title="Chi tiết lịch hẹn">
        </x-page-header>
        <div class="card">
            <div class="card-body">
                <h5 class="mb-4">
                    <img src="https://cdn-icons-png.flaticon.com/512/2838/2838779.png" width="24" class="me-2">
                    <strong>Chi tiết Lịch hẹn Khách hàng</strong>
                </h5>

                <div class="row">
                    <div class="col-md-6">
                        <p><strong>Khách hàng:</strong> {{ $appointment->customer->name }}</p>
                        <p><strong>Số điện thoại:</strong> {{ $appointment->customer->phone }}</p>
                        <p><strong>Email:</strong>{{ $appointment->customer->email }}</p>
                    </div>
                    <div class="col-md-6">
                        <p><strong>Nhân viên phụ trách:</strong> {{ $appointment->user->name }}</p>
                        <p><strong>Thời gian hẹn tiếp theo:</strong>
                            {{ \Carbon\Carbon::parse($appointment->scheduled_at)->format('d/m/Y - H:i') }}</p>
                        <p><strong>Trạng thái: </strong>
                            <span class="badge {{ $appointment->status_badge_class }}">
                                {{ $appointment->status_label }}
                            </span>
                        </p>
                    </div>
                </div>

                <hr class="m-0">
                <h6 class="my-3">
                    <i class="bi bi-journal-text me-1"></i> Lịch sử ghi chú & hẹn gặp:
                </h6>

                <div class="list-group mb-4">
                    <div class="list-group-item">
                        <small class="text-muted">
                            {{ \Carbon\Carbon::parse($appointment->scheduled_at)->format('d/m/Y - H:i') }}</small><br>
                        {{ $appointment->note }}.
                    </div>

                </div>

                <div class="d-flex justify-content-end">
                    <a href="/apppointment" class="btn btn-secondary me-2 btn-sm">Quay lại</a>

                    @if ($appointment->status == 'pending')
                        <!-- Nút huỷ -->
                        <button class="btn btn-danger btn-sm btn-change-status px-4 mx-4" data-id="{{ $appointment->id }}"
                            data-status="cancelled" data-title="Huỷ lịch hẹn?"
                            data-text="Bạn có chắc chắn muốn huỷ lịch hẹn này?" data-icon="warning"
                            data-confirm-text="Huỷ hẹn">
                            Huỷ
                        </button>

                        <!-- Nút hoàn thành -->
                        <button class="btn btn-success btn-sm btn-change-status px-4" data-id="{{ $appointment->id }}"
                            data-status="completed" data-title="Hoàn thành lịch hẹn?"
                            data-text="Xác nhận rằng lịch hẹn đã được hoàn tất." data-icon="success"
                            data-confirm-text="Xác nhận">
                            Hoàn thành
                        </button>
                    @endif

                </div>
            </div>
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
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        $(document).ready(function() {

            $('.btn-change-status').click(function() {
                const $btn = $(this);
                const id = $btn.data('id');
                const status = $btn.data('status');
                const title = $btn.data('title');
                const text = $btn.data('text');
                const icon = $btn.data('icon');
                const confirmText = $btn.data('confirm-text');

                Swal.fire({
                    title: title,
                    text: text,
                    icon: icon,
                    showCancelButton: true,
                    confirmButtonColor: status === 'cancelled' ? '#d33' : '#198754',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: confirmText,
                    cancelButtonText: 'Không',
                    customClass: {
                        popup: 'small-swal'
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: `/apppointment/update-status`,
                            method: 'POST',
                            data: {
                                _token: '{{ csrf_token() }}',
                                status: status,
                                id: id
                            },
                            success: function(res) {
                                Swal.fire('Thành công!', res.message || 'Đã cập nhật.',
                                    'success').then(() => {
                                    location.reload();
                                });
                            },
                            error: function(xhr) {
                                Swal.fire('Lỗi!', xhr.responseJSON?.message ||
                                    'Có lỗi xảy ra!', 'error');
                            }
                        });
                    }
                });
            });

        });
    </script>
@endpush
