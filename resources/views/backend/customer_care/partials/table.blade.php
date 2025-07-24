@foreach ($customer_cares as $index => $customer_care)
    <tr>
        <td>{{ ($customer_cares->currentPage() - 1) * $customer_cares->perPage() + $index + 1 }}</td>
        <td>{{ $customer_care->customer->name ?? '-' }}</td>
        <td>{{ $customer_care->user->name ?? '-' }}</td>
        <td>
            {{ $customer_care->channel->name ?? '-' }}
        </td>
        <td>{{ \Carbon\Carbon::parse($customer_care->care_date)->format('d/m/Y - H:i') }}</td>
        <td>{{ $customer_care->note }}</td>

        <td>
            {{ $customer_care->result->name ?? '-' }}
        </td>

        <td class="text-center">
            <div class="d-flex justify-content-center gap-1">
                {{-- Xem --}}
                <a href="/customer_care/view/{{ $customer_care->id }}" class="btn btn-outline-primary btn-sm"
                    title="Xem chi tiết lịch hẹn">
                    <i class="fas fa-eye"></i>
                </a>

                <a href="/customer_care/save/{{ $customer_care->id }}" class="btn btn-outline-warning btn-sm btn-edit-appointment"
                    title="Chỉnh sửa lịch hẹn">
                    <i class="fas fa-edit"></i>
                </a>

                <button type="button" class="btn btn-outline-danger btn-sm btn-delete-appointment"
                    data-id="{{ $customer_care->id }}" title="Xoá lịch hẹn">
                    <i class="fas fa-trash"></i>
                </button>
            </div>
        </td>


    </tr>
@endforeach
