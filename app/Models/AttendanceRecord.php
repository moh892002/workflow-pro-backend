<?php

namespace App\Models;

use App\Traits\RecycleBinTrait;
use Illuminate\Database\Eloquent\Model;

class AttendanceRecord extends Model
{
    use RecycleBinTrait;

    protected $fillable = [
        'user_id',
        'date',
        'check_in',
        'check_out',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'date' => 'date:Y-m-d',
            'check_in' => 'datetime',
            'check_out' => 'datetime',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
