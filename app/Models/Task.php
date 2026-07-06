<?php

namespace App\Models;

use App\Traits\RecycleBinTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property string $title
 * @property string $description
 * @property string $priority
 * @property string $status
 * @property Carbon $deadline_date
 * @property int|null $assigned_to
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 * @property-read User $user
 */
class Task extends Model
{
    use RecycleBinTrait;

    protected $fillable = [
        'title',
        'description',
        'priority',
        'deadline_date',
        'status',
        'assigned_to',
    ];

    protected $casts = [
        'deadline_date' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }
}
