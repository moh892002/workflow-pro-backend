<?php

namespace App\Models;

use App\Traits\RecycleBinTrait;
use Illuminate\Database\Eloquent\Model;

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

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewer_id');
    }
}
