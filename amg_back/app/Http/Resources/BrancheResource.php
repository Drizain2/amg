<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BrancheResource extends JsonResource
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
            'name' => $this->name,
            'address' => $this->address,
            'created_at' => $this->created_at->toDateTimeString(),

            // Nombre de produits en stock dans cette branche (évite le N+1 si eager loadé)
            'stocks_count' => $this->whenCounted('stocks'),

            // Users assignés (uniquement si chargé)
            'users' => UserResource::collection($this->whenLoaded('users')),
        ];
    }
}
