<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class UpdateRecordRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'user_id' => 'sometimes|required|exists:users,id',
            'transaction_type' => 'sometimes|required|in:salary,bonus,deduction,advance,overtime',
            'amount' => 'sometimes|required|numeric',
            'transaction_date' => 'sometimes|required|date',
            'notes' => 'sometimes|nullable|string',
        ];
    }
}
