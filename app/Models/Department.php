<?php

namespace App\Models;

use App\Traits\RecycleBinTrait;
use Illuminate\Database\Eloquent\Model;

class Department extends Model
{
    use RecycleBinTrait;

    public $fillable = ['name'];

    public function users()
    {
        return $this->hasMany(User::class);
    }
}
