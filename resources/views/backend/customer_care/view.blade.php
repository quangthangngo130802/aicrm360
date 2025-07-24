@extends('backend.layouts.app')

@section('content')
    <div class="py-4">
        <x-breadcrumb :breadcrumbs="[['label' => 'Chi tiết chăm sóc khách hàng']]" />

        <x-page-header title="Chi tiết chăm sóc khách hàng">
        </x-page-header>
        <div class="card p-3" style=" background: #f0f2f5;">
            <h4 class="fw-bold mb-4">📝 Nhật ký chăm sóc khách hàng: <span
                    class="text-primary">{{ $customerCare->customer->name }}</span></h4>

            <!-- Bộ lọc -->
            <div class="row g-3 align-items-end mb-5 filter-section">
                <div class="col-md-4">
                    <label class="form-label">Từ ngày</label>
                    <input type="date" class="form-control rounded-3" />
                </div>
                <div class="col-md-4">
                    <label class="form-label">Đến ngày</label>
                    <input type="date" class="form-control rounded-3" />
                </div>
                <div class="col-md-4">
                    <label class="form-label">Kết quả</label>
                    <select class="form-select rounded-3" id="search-status">
                        <option value="">Kết quả</option>
                        @foreach ($results as $item)
                            <option value="{{ $item->id }}">{{ $item->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <!-- Timeline -->
            <div class="timeline-container">

                <!-- Entry 1 -->
                @forelse ($customerCares as $item)
                    <div class="timeline-entry">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <div class="timeline-title">
                                <i class="bi bi-telephone-fill text-danger me-2"></i>
                            </div>
                            <span class="badge badge-soft badge-soft-warning">{{ $item->result->name }}</span>
                        </div>
                        <div class="timeline-meta">
                            📅 <strong>{{ \Carbon\Carbon::parse($item->care_date)->format('d/m/Y - H:i') }}</strong>
                            &nbsp;|&nbsp; 👤 Nhân viên: {{ $customerCare->user->name }}<strong></strong>
                        </div>
                        <div class="timeline-note">
                            {{ $item->note }}
                        </div>
                    </div>
                @empty
                @endforelse

                <!-- Entry 2 -->
                {{-- <div class="timeline-entry">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <div class="timeline-title">
                            <i class="bi bi-envelope-paper-fill text-success me-2"></i>Gửi demo Mini CRM
                        </div>
                        <span class="badge badge-soft badge-soft-success">Đã mua</span>
                    </div>
                    <div class="timeline-meta">
                        📅 <strong>21/07/2025</strong> &nbsp;|&nbsp; 👤 Nhân viên: <strong>Trần Thị Mai</strong>
                    </div>
                    <div class="timeline-note">
                        Khách đã xem demo và chốt mua gói 5Tr. Đã gửi hợp đồng.
                    </div>
                </div>

                <!-- Entry 3 -->
                <div class="timeline-entry">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <div class="timeline-title">
                            <i class="bi bi-cash-coin text-primary me-2"></i>Gọi nhắc thanh toán
                        </div>
                        <span class="badge badge-soft badge-soft-success">Đã mua</span>
                    </div>
                    <div class="timeline-meta">
                        📅 <strong>24/07/2025</strong> &nbsp;|&nbsp; 👤 Nhân viên: <strong>Lê Quốc Huy</strong>
                    </div>
                    <div class="timeline-note">
                        Khách xác nhận đã chuyển khoản. Hẹn triển khai từ 26/07.
                    </div>
                </div> --}}
            </div>

            <!-- Nhắc định kỳ -->
            <div class="form-check mt-4 mb-4">
                <input class="form-check-input" type="checkbox" checked disabled id="reminder-check" />
                <label class="form-check-label" for="reminder-check">
                    Nhắc chăm sóc định kỳ: Lịch nhắc mỗi 3 tháng để upsell gói nâng cao.
                </label>
            </div>

            <!-- Buttons -->
            <div class="d-flex flex-wrap gap-3 mt-3">
                <a href="tel:{{ $customerCare->customer->phone }}" class="btn btn-primary btn-rounded">
                    <i class="fas fa-phone-alt me-1"></i> Gọi
                </a>
                <a href="/customer_care/save" class="btn btn-success btn-rounded">
                    <i class="fas fa-plus-circle me-1"></i> Thêm lần chăm sóc
                </a>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <style>
        .timeline-container {
            border-left: 4px solid #0d6efd;
            padding-left: 2rem;
            position: relative;
        }

        .timeline-entry {
            position: relative;
            margin-bottom: 2rem;
            padding: 1.5rem;
            background: #fff;
            border-radius: 1rem;
            box-shadow: 0 4px 14px rgba(0, 0, 0, 0.06);
            transition: all 0.3s ease;
        }

        .timeline-entry:hover {
            transform: translateY(-3px);
            box-shadow: 0 6px 18px rgba(0, 0, 0, 0.08);
        }

        .timeline-entry::before {
            content: "";
            position: absolute;
            top: 20px;
            left: -32px;
            width: 18px;
            height: 18px;
            background: #0d6efd;
            border: 3px solid #fff;
            border-radius: 50%;
            z-index: 2;
        }

        .timeline-title {
            font-weight: 600;
            font-size: 1.1rem;
            color: #212529;
        }

        .timeline-meta {
            font-size: 0.875rem;
            color: #666;
            margin-top: 4px;
        }

        .timeline-note {
            margin-top: 10px;
            font-size: 0.95rem;
            color: #495057;
            font-style: italic;
        }

        .badge-soft {
            font-size: 0.75rem;
            padding: 5px 12px;
            border-radius: 999px;
        }

        .badge-soft-success {
            background-color: #d1e7dd;
            color: #146c43;
        }

        .badge-soft-warning {
            background-color: #fff3cd;
            color: #997404;
        }

        .btn-rounded {
            border-radius: 50px;
            padding-left: 1.4rem;
            padding-right: 1.4rem;
            font-weight: 500;
        }

        .form-check-label {
            font-size: 0.92rem;
            color: #555;
        }

        .filter-section label {
            font-weight: 600;
            font-size: 0.95rem;
        }
    </style>
@endpush

@push('scripts')
@endpush
