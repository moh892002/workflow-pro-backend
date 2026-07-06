<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class StoreUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'fullname' => 'required|string',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
            'role' => 'required|in:ADMIN,HR_MANAGER,OPS_MANAGER,SALES_DIRECTOR,EMPLOYEE',
            'department_id' => 'nullable|exists:departments,id',
            'job_title' => 'required|string',
            'image' => 'nullable|string',
            'username' => 'required|unique:users,username',
            'salary' => 'required|integer',
        ];
    }
}
