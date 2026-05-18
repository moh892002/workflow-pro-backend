<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\RecycleBinTrait;

class PerformanceReview extends Model
{
    use RecycleBinTrait;

    public function user() {
        return $this->belongsTo(User::class);
    }

    public function reviewer() {
        return $this->belongsTo(User::class, 'reviewer_id');
    }
}
