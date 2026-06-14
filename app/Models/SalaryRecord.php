<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\RecycleBinTrait;

class SalaryRecord extends Model
{
    use RecycleBinTrait;

    protected $fillable = [
            'user_id',
            'transaction_type',
            'amount',
            'transaction_date',
            'notes',
        ];
    public function user() {
        return $this->belongsTo(User::class);
    }

    // public function processor() {
    //     return $this->belongsTo(User::class, 'processed_by');
    // }

    // public function transactions() {
    //     return $this->hasMany(Transaction::class, 'salary_record_id', 'transaction_id');
    // }
}
