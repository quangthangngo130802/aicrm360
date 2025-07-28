<?php

namespace App\Http\Requests;

use App\Models\User;
use App\Traits\ValidatesMediaPaths;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Log;

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
            'code' => ['nullable', 'string', 'max:50'],
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'string', 'max:255'],
            'password' => [$id ? 'nullable' : 'required', 'string', 'max:255', 'min:8'],
            'phone' => ['nullable', 'string', 'regex:/^0\d{9}$/'],
            'address' => ['nullable', 'string', 'max:255'],
            'birthday' => ['nullable', 'date_format:d-m-Y', 'before:today'],
            'gender' => ['required', 'in:male,female,other'],
        ];
    }
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $id = $this->route('id');

            $userId = $this->input('user_id') ?? auth()->id();
            $subdomain = User::where('id', $userId)->value('subdomain');

            if (!$subdomain) return;

            $email = $this->input('email');
            $phone = $this->input('phone');


            if ($email) {
                $exists = User::where('email', $email)
                    ->where('subdomain', $subdomain)
                    ->when($id, fn($q) => $q->where('id', '<>', $id))
                    ->exists();

                if ($exists) {
                    Log::info('Email đã tồn tại');
                    $validator->errors()->add('email', 'Email đã tồn tại trong subdomain này.');
                }
            }

            if ($phone) {
                $exists = User::where('phone', $phone)
                    ->where('subdomain', $subdomain)
                    ->when($id, fn($q) => $q->where('id', '<>', $id))
                    ->exists();

                if ($exists) {
                    Log::info('Số điện thoại đã tồn tại');
                    $validator->errors()->add('phone', 'Số điện thoại đã tồn tại trong subdomain này.');
                }
            }
        });
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
