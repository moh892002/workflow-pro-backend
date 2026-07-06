<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Traits\RecycleBinTrait;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Carbon;
use Laravel\Sanctum\HasApiTokens;

/**
 * @property int $id
 * @property string $fullname
 * @property string $email
 * @property string $password
 * @property string $role
 * @property int|null $department_id
 * @property string $job_title
 * @property string|null $image
 * @property string $username
 * @property int $salary
 * @property string|null $email_verified_at
 * @property string|null $remember_token
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 * @property-read string|null $image_url
 * @property-read Department|null $department
 * @property-read Collection<int, Task> $tasks
 * @property-read Collection<int, SalaryRecord> $salaryRecords
 */
class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, RecycleBinTrait;

    protected $appends = ['image_url'];

    protected $hidden = ['password', 'remember_token'];

    public function getImageUrlAttribute(): ?string
    {
        return $this->image ? asset('images/users/'.$this->image) : null;
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
        'salary',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class, 'assigned_to');
    }

    public function salaryRecords(): HasMany
    {
        return $this->hasMany(SalaryRecord::class);
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }
}
