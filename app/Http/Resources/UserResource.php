<?php

namespace App\Http\Resources;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin User */
class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'fullname' => $this->fullname,
            'email' => $this->email,
            'role' => $this->role,
            'department_id' => $this->department_id,
            'job_title' => $this->job_title,
            'image_url' => $this->image_url,
            'username' => $this->username,
            'salary' => $this->salary,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'deleted_at' => $this->deleted_at,
            // Include department data if needed
            'department' => $this->whenLoaded('department', fn () => $this->department?->only('id', 'name')),
        ];
    }
}
