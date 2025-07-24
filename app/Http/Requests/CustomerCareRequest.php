<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CustomerCareRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $id = $this->route('id'); // nếu cần dùng để loại trừ chính nó khi validate

        return [
            'customer_id' => ['required', 'exists:customers,id'],
            'user_id'     => ['required', 'exists:users,id'],
            'care_date'   => ['required', 'date'],
            'channel_id'  => ['required', 'exists:channels,id'],
            'result_id'   => ['required', 'exists:results,id'],
            'note'        => ['required'],
        ];
    }

    public function messages(): array
    {
        return __('request.messages');
    }

    public function attributes(): array
    {
        return [
            'customer_id' => 'Khách hàng',
            'user_id'     => 'Nhân viên phụ trách',
            'care_date'   => 'Ngày chăm sóc',
            'channel_id'  => 'Kênh chăm sóc',
            'result_id'   => 'Kết quả',
            'Chi tiết'    => 'Chi tiết',
        ];
    }
}
