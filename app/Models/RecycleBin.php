<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RecycleBin extends Model
{
    // Casting JSONB data to an array automatically
    protected $casts = [
        'deleted_data' => 'array',
    ];

    public function user() {
        return $this->belongsTo(User::class, 'deleted_by');
    }
}
