<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StorePerformanceReviewRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'user_id' => [
                'required',
                'exists:users,id',
                Rule::notIn([$this->user()->id]),
            ],
            'reviewer_id' => 'sometimes|exists:users,id',
            'score' => 'required|integer|min:0|max:100',
            'review_period' => 'required|string',
            'ai_generated_feedback' => 'nullable|string',
            'final_feedback' => 'nullable|string',
            'status' => ['required', Rule::in(['draft', 'pending', 'completed'])],
        ];
    }
}
