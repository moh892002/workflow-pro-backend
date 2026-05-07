<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PerformanceReview extends Model
{
    public function user() {
        return $this->belongsTo(User::class);
    }

    public function reviewer() {
        return $this->belongsTo(User::class, 'reviewer_id');
    }
}
