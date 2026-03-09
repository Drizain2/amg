<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TransfertStockMovementRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            // Ce que l'utilisateur connaît : le produit et les deux branches
            // Pas besoin de connaître les stock_id internes
            'product_id'       => 'required|integer|exists:products,id',
            'branche_source_id'=> 'required|integer|exists:branches,id',
            'branche_dest_id'  => 'required|integer|exists:branches,id|different:branche_source_id',
            'quantity'         => 'required|integer|min:1',
            'reason'           => 'nullable|string|max:255',
        ];
    }

    public function messages(): array
    {
        return [
            'product_id.required'        => 'Le produit est obligatoire.',
            'product_id.exists'          => 'Ce produit n\'existe pas.',
            'branche_source_id.required' => 'La branche source est obligatoire.',
            'branche_dest_id.required'   => 'La branche destination est obligatoire.',
            'branche_dest_id.different'  => 'La branche source et destination doivent être différentes.',
            'quantity.min'               => 'La quantité doit être au moins 1.',
        ];
    }
}
