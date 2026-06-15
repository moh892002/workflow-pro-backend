<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdatePerformanceReviewRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'score' => 'sometimes|integer|min:0|max:100',
            'review_period' => 'sometimes|string',
            'ai_generated_feedback' => 'sometimes|string',
            'final_feedback' => 'sometimes|string',
            'status' => ['sometimes', Rule::in(['draft', 'pending', 'completed'])],
            'reviewer_id' => 'sometimes|exists:users,id',
        ];
    }
}
