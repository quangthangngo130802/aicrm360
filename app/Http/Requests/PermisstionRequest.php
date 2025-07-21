<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PermisstionRequest extends FormRequest
{
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
        $id = $this->input('id', null);

        return [
            'name' => "required|unique:permissions,name,$id",
            'vi_name' => "required|unique:permissions,vi_name,$id",
            'group_name' => "required",
            'id' => "nullable|exists:permissions,id",
        ];
    }

    public function messages()
    {
        return __('request.messages');
    }

    public function attributes()
    {
        return [
            'name' => 'Tên quyền',
            'vi_name' => 'Tên quyền tiếng việt',
            'group_name' => 'Nhóm quyền',
        ];
    }
}
