<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
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
            // Include department data if needed
            'department' => $this->whenLoaded('department'),
        ];
    }
}