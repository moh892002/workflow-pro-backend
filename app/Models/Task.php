<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Task extends Model
{
    protected $fillable = [
        'title',
        'description',
        'priority',
        'deadline_date',
        'assigned_to',
        'project_id',
    ];

//    public function project() {
//        return $this->belongsTo(Project::class);
//    }

    public function user() {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    // In the diagram, tasks can have tags (task_tags)
    // public function tags() {
    //     return $this->belongsToMany(Tag::class, 'task_tags');
    // }
}
