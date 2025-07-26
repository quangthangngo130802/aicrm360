<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name'    => ['required', 'string', 'max:255'],
            'phone'   => ['required', 'string', 'max:20', 'regex:/^0\d{9,10}$/', 'unique:users,phone'],
            'email'   => ['required', 'email', 'max:255', 'unique:users,email'],
            'subdomain'   => ['required', 'max:255', 'unique:users,subdomain', 'regex:/^[a-z0-9]+$/'],
            'password' => ['required', 'string', 'min:8'],
            'company' => ['required', 'string', 'max:255'],
            'gender' => ['nullable', 'in:male,female,other'],
            'field'   => ['nullable', 'string', 'max:100'],
            'demand'  => ['nullable', 'string', 'max:1000'],
        ];
    }

    public function messages(): array
    {
        return __('request.messages'); // hoặc bạn có thể ghi cụ thể nếu chưa có file lang
    }

    public function attributes(): array
    {
        return [
            'name'    => 'Họ và tên',
            'phone'   => 'Số điện thoại',
            'email'   => 'Email',
            'company' => 'Công ty',
            'field'   => 'Lĩnh vực',
            'demand'  => 'Nhu cầu',
            'subdomain'=> 'Tên đăng nhập',
            'gender'  => 'Giới tính',
            'password'=> 'Mật khẩu',
        ];
    }
}
