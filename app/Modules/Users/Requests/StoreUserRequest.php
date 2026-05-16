<?php

namespace App\Modules\Users\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreUserRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'fullname' => 'required|string',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
            'role' => 'required|in:ADMIN,HR_MANAGER,EMPLOYEE',
            'department_id' => 'nullable|exists:departments,id',
            'job_title' => 'required|string',
            'image' => 'nullable|image|max:2048',
            'username' => 'required|unique:users,username',
            'salary' => 'required|integer',
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}