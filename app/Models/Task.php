<?php

namespace App\Models;

use App\Traits\RecycleBinTrait;
use Illuminate\Database\Eloquent\Model;

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

    public function user()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }
}
