<?php

namespace App\Models;

use App\Traits\RecycleBinTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $user_id
 * @property int $reviewer_id
 * @property int $score
 * @property string $review_period
 * @property string|null $ai_generated_feedback
 * @property string|null $final_feedback
 * @property string $status
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 * @property-read User $user
 * @property-read User $reviewer
 */
class PerformanceReview extends Model
{
    use RecycleBinTrait;

    protected $fillable = [
        'user_id',
        'reviewer_id',
        'score',
        'review_period',
        'ai_generated_feedback',
        'final_feedback',
        'status',
    ];

    protected $casts = [
        'score' => 'integer',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewer_id');
    }
}
