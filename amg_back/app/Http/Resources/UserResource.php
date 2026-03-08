<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

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
            'id'         => $this->id,
            'name'       => $this->name,
            'last_name'  => $this->last_name,
            'email'      => $this->email,
            'role'       => $this->role,

            // Branches assignées — uniquement si la relation est chargée (évite N+1)
            'branches'   => BrancheResource::collection($this->whenLoaded('branches')),

            'created_at' => $this->created_at->toDateTimeString(),
        ];
    }
}
