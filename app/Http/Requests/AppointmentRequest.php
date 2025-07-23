<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AppointmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        // Lấy id từ route nếu có (dành cho khi update)
        $id = $this->route('id');

        return [
            'customer_id'    => ['required', 'exists:customers,id'],
            'user_id'        => ['required', 'exists:users,id'],
            'scheduled_at'   => ['required'],
            'note'           => ['nullable', 'string', 'max:500'],
            'status'         => ['required', 'in:pending,completed,cancelled'],
        ];
    }

    public function messages()
    {
        return __('request.messages'); // Sử dụng file lang nếu có
    }

    public function attributes(): array
    {
        return [
            'customer_id'   => 'Khách hàng',
            'scheduled_at'  => 'Thời gian hẹn',
            'note'          => 'Ghi chú',
            'status'        => 'Trạng thái',
            'user_id'       => 'Người phụ trách',
        ];
    }
}
