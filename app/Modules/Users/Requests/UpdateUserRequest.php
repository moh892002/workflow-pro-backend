<?php

namespace App\Modules\Users\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $userId = $this->route('id');

        return [
            'fullname' => 'sometimes|required|string',

            'email' => 'sometimes|required|email|unique:users,email,' . $userId,

            'password' => 'sometimes|required|string|min:6',

            'role' => 'sometimes|required|in:ADMIN,HR_MANAGER,EMPLOYEE,OPS_MANAGER,SALES_DIRECTOR',

            'department_id' => 'nullable|exists:departments,id',

            'job_title' => 'sometimes|required|string',

            'image' => 'nullable|image|max:2048',

            'username' => 'sometimes|required|unique:users,username,' . $userId,

            'salary' => 'sometimes|required|integer',
        ];
    }
}