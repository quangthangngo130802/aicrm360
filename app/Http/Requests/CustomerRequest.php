<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CustomerRequest extends FormRequest
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
        $id = $this->route('id', null); // dùng cho update

        return [
            'code'               => ['nullable', 'string', 'max:50', "unique:customers,code,{$id}"],
            'name'               => ['required', 'string', 'max:255'],
            'phone'              => ['required', 'string', 'regex:/^0\d{9}$/', "unique:customers,phone,{$id}"],
            'email'              => ['required', 'email', 'max:255', "unique:customers,email,{$id}"],
            'area'               => ['required', 'string', 'max:255'],
            'birthday'           => ['nullable', 'date_format:d-m-Y', 'before:today'],
            'gender'             => ['required', 'in:male,female,other'],
            'source_id'          => ['nullable'],

            'customer_category_id'           => ['required'],
            // Các trường khác cho phép null
            'company_name'      => ['nullable', 'string', 'max:255'],
            'company_phone'     => ['nullable', 'string', 'regex:/^0\d{9}$/'],
            'company_tax_code'  => ['nullable', 'string', 'max:100'],
            'address'           => ['nullable', 'string', 'max:255'],
            'user_id'           => ['nullable', 'exists:users,id'],
            'tax_code'          => ['nullable', 'string', 'max:100'],
            'demand'            => ['nullable', 'string'],
            'facebook_link'     => ['nullable', 'url'],
            'youtube_link'      => ['nullable', 'url'],
            'instagram_link'    => ['nullable', 'url'],
        ];
    }

    public function messages()
    {
        return __('request.messages'); // Sử dụng file lang nếu có
    }

    public function attributes(): array
    {
        return [
            'code'              => 'Mã khách hàng',
            'name'              => 'Tên khách hàng',
            'phone'             => 'Số điện thoại',
            'email'             => 'Email',
            'gender'            => 'Giới tính',
            'career'            => 'Nghề nghiệp',
            'area'              => 'Địa chỉ',
            'source_id'         => 'Nguồn khách hàng',
            'company_name'      => 'Tên công ty',
            'company_phone'     => 'SĐT công ty',
            'company_tax_code'  => 'Mã số thuế công ty',
            'address'           => 'Địa chỉ',
            'user_id'           => 'Nhân viên phụ trách',
            'tax_code'          => 'Mã số thuế',
            'demand'            => 'Nhu cầu',
            'facebook_link'     => 'Link Facebook',
            'youtube_link'      => 'Link Youtube',
            'instagram_link'    => 'Link Instagram',
            'customer_category_id' => 'Loại khách hàng'
        ];
    }
}
