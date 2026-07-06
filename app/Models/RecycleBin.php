<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property string $deleted_table_name
 * @property string $deleted_model
 * @property int $deleted_item_id
 * @property array $deleted_data
 * @property Carbon $deleted_at
 * @property int|null $deleted_by
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read User|null $user
 */
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

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'deleted_by');
    }
}
