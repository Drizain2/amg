<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
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
            'name'        => 'sometimes|required|string|max:255',
            'last_name'   => 'sometimes|required|string|max:255',
            'role'        => ['sometimes', 'required', Rule::in(['manager', 'operator'])],

            // Réassigner les branches (remplace toutes les branches existantes)
            'branche_ids'   => 'sometimes|required|array|min:1',
            'branche_ids.*' => 'integer|exists:branches,id',
        ];
    }

    public function messages(): array
    {
        return [
            'role.in'               => 'Le rôle doit être "manager" ou "operator".',
            'branche_ids.required'  => 'Vous devez assigner au moins une branche.',
            'branche_ids.*.exists'  => 'Une des branches sélectionnées n\'existe pas.',
        ];
    }
}
