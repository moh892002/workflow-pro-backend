<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    public function salaryRecord() {
        return $this->belongsTo(SalaryRecord::class, 'salary_record_id', 'transaction_id');
    }
}
