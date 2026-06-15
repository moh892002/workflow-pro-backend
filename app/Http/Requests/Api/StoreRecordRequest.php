<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class StoreRecordRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'user_id' => 'required|exists:users,id',
            'transaction_type' => 'required|in:salary,bonus,deduction,advance,overtime',
            'amount' => 'required|numeric',
            'transaction_date' => 'required|date',
            'notes' => 'nullable|string',
        ];
    }
}
