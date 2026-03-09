<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreStockMovementRequest extends FormRequest
{
    /**
     * Admin, manager et operator peuvent créer des mouvements —
     * mais uniquement sur leurs branches accessibles (vérifié dans le controller).
     */
    public function authorize(): bool
    {
        return auth()->check();
    }

   public function rules(): array
    {
        return [
            'product_id' => 'required|integer|exists:products,id',
            'branche_id' => 'required|integer|exists:branches,id',
            'type'       => ['required', Rule::in(['in', 'out', 'adjustment'])],
            'quantity'   => 'required|integer|min:1',
            'reason'     => 'nullable|string|max:255',
        ];
    }

    public function messages(): array
    {
        return [
            'product_id.required' => 'Le produit est obligatoire.',
            'product_id.exists'   => 'Ce produit n\'existe pas.',
            'branche_id.required' => 'La branche est obligatoire.',
            'branche_id.exists'   => 'Cette branche n\'existe pas.',
            'type.required'       => 'Le type de mouvement est obligatoire.',
            'type.in'             => 'Le type doit être "in", "out" ou "adjustment".',
            'quantity.min'        => 'La quantité doit être au moins 1.',
        ];
    }
}
