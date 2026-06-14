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
        'project_id',
    ];

    //    public function project() {
    //        return $this->belongsTo(Project::class);
    //    }

    public function user()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    // In the diagram, tasks can have tags (task_tags)
    // public function tags() {
    //     return $this->belongsToMany(Tag::class, 'task_tags');
    // }
}
