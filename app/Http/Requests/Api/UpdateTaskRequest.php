<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class UpdateTaskRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => 'sometimes|required|string|max:255',
            'description' => 'sometimes|required|string',
            'priority' => 'sometimes|required|in:LOW,MEDIUM,HIGH,URGENT',
            'status' => 'sometimes|required|in:completed,pending,in_progress',
            'deadline_date' => 'sometimes|required|date_format:Y-m-d',
            'assigned_to' => 'sometimes|nullable|exists:users,id',
        ];
    }
}
