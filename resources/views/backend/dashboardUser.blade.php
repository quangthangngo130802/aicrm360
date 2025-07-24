@extends('backend.layouts.app')

@section('content')
    <div class="">
        <h3 class="mb-4"> Tổng quan</h3>

        <div class="row g-4 mb-4">
            <div class="col-md-3">
                <div class="card p-3 bg-success text-white">
                    <h6>KH chăm sóc hôm nay</h6>
                    <h2>{{ $customerCareCount }}</h2>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card p-3 bg-primary text-white">
                    <h6>Lịch hẹn hôm nay</h6>
                    <h2>{{ $appointmentCount }}</h2>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card p-3 bg-warning text-white">
                    <h6>Lịch hẹn sắp diễn ra</h6>
                    <h2>{{ $appointmentNextCount }}</h2>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card p-3 bg-info text-white">
                    <h6>Khách hàng trong tuần</h6>
                    <h2>{{ $customerCount }}</h2>
                </div>
            </div>
        </div>

        <div class="card mb-4">
            <div class="card-header "><strong>👥 Khách hàng tương tác hôm nay</strong></div>
            <div class="card-body">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Tên KH</th>
                            <th>Điện thoại</th>
                            <th>Lý do</th>
                            <th>Lịch hẹn</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($customerNow as $customer)
                            @foreach ($customer->appointments as $appointment)
                                <tr>
                                    <td>{{ $customer->name }}</td>
                                    <td>{{ $customer->phone }}</td>
                                    <td>{{ $appointment->note }}</td>
                                    <td>{{ \Carbon\Carbon::parse($appointment->scheduled_at)->format('H:i') }} hôm nay</td>
                                </tr>
                            @endforeach
                        @empty
                            <tr>
                                <td colspan="4" class="text-center text-muted">
                                    Không có khách hàng tương tác hôm nay.
                                </td>
                            </tr>
                        @endforelse

                    </tbody>
                </table>
            </div>
        </div>

        <div class="card mb-4">
            <div class="card-header "><strong>📆 Lịch hẹn sắp diễn ra</strong></div>
            <div class="card-body">
                <ul class="list-group">
                    @forelse($appointmentNext as $key => $appointment)
                        <li class="list-group-item {{ $loop->first ? 'highlight' : '' }}">
                            <strong>{{ \Carbon\Carbon::parse($appointment->scheduled_at)->format('H:i d/m') }}:</strong>
                            Gọi lại {{ $appointment->customer->name ?? '---' }}
                            @if (!empty($appointment->customer->company_name))
                                ({{ $appointment->customer->company_name }})
                            @endif
                            –
                            {{ $appointment->note }}
                        </li>
                    @empty
                        <li class="list-group-item text-muted">Không có lịch hẹn sắp tới.</li>
                    @endforelse

                </ul>
            </div>
        </div>



        <!-- Khách hàng mới trong tuần -->
        {{-- <div class="card">
            <div class="card-header"><strong>🆕 Khách hàng mới trong tuần</strong></div>
            <div class="card-body">
                <ul class="mb-0">
                    @forelse($customers as $customer)
                        @php
                            $appointmentNote = optional($customer->appointments->first())->note;
                        @endphp
                        <li>
                            {{ $customer->name }} - {{ $appointmentNote ?? 'Không có ghi chú' }}
                        </li>
                    @empty
                        <li>Không có khách hàng mới nào trong tuần.</li>
                    @endforelse
                </ul>
            </div>
        </div> --}}

    </div>
@endsection

@push('styles')
    <style>
        .card {
            border-radius: 1rem;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
        }

        .highlight {
            border-left: 5px solid #0d6efd;
            background: #e7f1ff;
        }
    </style>
@endpush

@push('scripts')
@endpush
