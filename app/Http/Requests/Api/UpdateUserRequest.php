<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class UpdateUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $userId = $this->route('user');

        return [
            'fullname' => 'sometimes|required|string',
            'email' => 'sometimes|required|email|unique:users,email,' . $userId,
            'password' => 'sometimes|required|string|min:6',
            'role' => 'sometimes|required|in:ADMIN,HR_MANAGER,EMPLOYEE',
            'department_id' => 'nullable|exists:departments,id',
            'job_title' => 'sometimes|required|string',
            'image' => 'nullable|string',
            'username' => 'sometimes|required|unique:users,username,' . $userId,
            'salary' => 'sometimes|required|integer',
        ];
    }
}
