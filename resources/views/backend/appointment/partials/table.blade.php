@foreach ($appointments as $index => $appointment)
    <tr>
        <td>{{ ($appointments->currentPage() - 1) * $appointments->perPage() + $index + 1 }}</td>
        <td>{{ $appointment->customer->name ?? '-' }}</td>
        <td>{{ $appointment->note }}</td>
        <td>{{ $appointment->user->name ?? '-' }}</td>
        <td>{{ \Carbon\Carbon::parse($appointment->scheduled_at)->format('d/m/Y - H:i') }}</td>
        <td>
            <span class="badge {{ $appointment->status_badge_class }}">
                {{ $appointment->status_label }}
            </span>
        </td>
        <td class="text-center">
            <div class="d-flex justify-content-center gap-1">
                {{-- Xem --}}
                <a href="/apppointment/view/{{ $appointment->id }}" class="btn btn-outline-primary btn-sm"
                    title="Xem chi tiết lịch hẹn">
                    <i class="fas fa-eye"></i>
                </a>

                {{-- Sửa --}}
                <a href="/apppointment/edit/{{ $appointment->id }}" class="btn btn-outline-warning btn-sm"
                    title="Chỉnh sửa lịch hẹn">
                    <i class="fas fa-edit"></i>
                </a>

                {{-- Xoá --}}
                <button type="button" class="btn btn-outline-danger btn-sm btn-delete-appointment"
                    data-id="{{ $appointment->id }}" title="Xoá lịch hẹn">
                    <i class="fas fa-trash"></i>
                </button>
            </div>
        </td>


    </tr>
@endforeach
