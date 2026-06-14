<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use App\Traits\RecycleBinTrait;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasApiTokens, RecycleBinTrait;
    protected $appends = ['image_url'];

    protected $hidden = ['password', 'remember_token'];


    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */

    public function getImageUrlAttribute(): string|null
    {
        return $this->image ? asset('images/' . $this->image) : null;
    }

    protected $fillable = [
        'fullname',
        'email',
        'password',
        'role',
        'department_id',
        'job_title',
        'image',
        'username',
        'salary'
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    // Projects managed by this user
    // public function managedProjects() {
    //     return $this->hasMany(Project::class, 'managed_by');
    // }

    // Projects where the user is a member
    public function projects() {
        return $this->belongsToMany(Project::class, 'project_members')
                    ->withPivot('role_in_project');
    }

    public function tasks() {
        return $this->hasMany(Task::class, 'assigned_to');
    }

    public function salaryRecords() {
        return $this->hasMany(SalaryRecord::class);
    }

    public function department(){
        return $this->belongsTo(Department::class);
    }

}
