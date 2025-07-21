<?php

namespace App\Http\Requests;

use App\Traits\ValidatesMediaPaths;
use Illuminate\Foundation\Http\FormRequest;

class EmployeeRequest extends FormRequest
{
    use ValidatesMediaPaths;
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $id = $this->route('id', null);
        return [
            'code' => ['nullable', 'string', 'max:50', "unique:users,code,{$id}"],
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'string', 'max:255'],
            'password' => [$id ? 'nullable' : 'required', 'string', 'max:255', 'min:8', 'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{8,}$/'],
            'phone' => ['nullable', 'string', 'regex:/^0\d{9}$/', "unique:users,phone,{$id}"],
            'address' => ['nullable', 'string', 'max:255'],
            'birthday' => ['nullable', 'date_format:d-m-Y', 'before:today'],
            'gender' => ['required', 'in:male,female,other'],
        ];
    }

    public function messages()
    {
        return __('request.messages');
    }

    public function attributes(): array
    {
        return [
            'code' => 'Mã nhân viên',
            'name' => 'Họ tên',
            'email' => 'Email',
            'password' => 'Mật khẩu',
            'phone' => 'Số điện thoại',
            'address' => 'Địa chỉ',
            'birthday' => 'Ngày sinh',
            'gender' => 'Giới tính',
        ];
    }

}
