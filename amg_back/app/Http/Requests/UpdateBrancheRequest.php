<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateBrancheRequest extends FormRequest
{
     /**
     * Seul l'admin peut modifier une branche.
     */
    public function authorize(): bool
    {
        return auth()->user()->isAdmin();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name'    => 'sometimes|required|string|max:255',
            'address' => 'nullable|string|max:255',
        ];
    }
    public function messages(): array
    {
        return [
            'name.required' => 'Le nom de la branche est obligatoire.',
            'name.max'      => 'Le nom ne peut pas dépasser 255 caractères.',
        ];
    }
}
