<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\RecycleBinTrait;

class Department extends Model
{
    //

    public $fillable = ['name'];

    public function users() {
        return $this->hasMany(User::class);
    }
}
