<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RecycleBin extends Model
{
    // Casting JSONB data to an array automatically
    protected $casts = [
        'deleted_data' => 'array',
    ];

    protected $fillable = [
        'deleted_table_name',
        'deleted_model',
        'deleted_item_id',
        'deleted_data',
        'deleted_at',
        'deleted_by',
    ];

    public function user() {
        return $this->belongsTo(User::class, 'deleted_by');
    }
}
