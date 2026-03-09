<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StockMovementResource extends JsonResource
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
            'reference' => $this->reference,
            'type' => $this->type,
            'quantity' => $this->quantity,
            'reason' => $this->reason,

            // Stock concerné avec le produit et la branche (si chargé)
            'stock' => $this->whenLoaded('stock', fn() => [
                'id' => $this->stock->id,
                'product' => $this->stock->product?->name,
                'branche' => $this->stock->branche?->name,
                'quantity_actuelle' => $this->stock->quantity,
            ]),

            'user' => $this->whenLoaded('user', fn() => [
                'id' => $this->user->id,
                'name' => $this->user->name . ' ' . $this->user->last_name,
            ]),

            'created_at' => $this->created_at->toDateTimeString(),
        ];
    }
}
