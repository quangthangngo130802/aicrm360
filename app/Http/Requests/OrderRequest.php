<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class OrderRequest extends FormRequest
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
        $id = $this->route('id', null); // nếu dùng update thì lấy id ra để không bị lỗi unique
        return [
            'code'          => ['nullable', 'string', 'max:50', "unique:orders,code,{$id}"],
            'customer_id'   => ['required', 'exists:customers,id'],
            'user_id'       => ['nullable', 'exists:users,id'],
            'total_amount'  => ['required', 'min:0'],
            'status'        => ['required', 'in:pending,processing,completed,cancelled'],
            'note'          => ['required'],
        ];
    }

    public function messages(): array
    {
        return __('request.messages');
    }

    public function attributes(): array
    {
        return [
            'code'          => 'Mã đơn hàng',
            'customer_id'   => 'Khách hàng',
            'user_id'       => 'Nhân viên phụ trách',
            'status'        => 'Trạng thái đơn hàng',
            'total_amount'  => 'Tổng tiền',
            'note'          => 'Thông tin đơn hàng',
        ];
    }
}
