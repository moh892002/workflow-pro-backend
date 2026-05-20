<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RecycleBinResource extends JsonResource
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
            'deleted_table_name' => $this->deleted_table_name,
            'deleted_model' => $this->deleted_model,
            'deleted_item_id' => $this->deleted_item_id,
            'deleted_data' => $this->deleted_data,
            'deleted_at' => $this->deleted_at,
            'deleted_by' => $this->deleted_by,
            'user' => $this->whenLoaded('user'),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
