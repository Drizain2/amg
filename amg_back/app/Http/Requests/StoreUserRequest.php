<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreUserRequest extends FormRequest
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
            'name'        => 'required|string|max:255',
            'last_name'   => 'required|string|max:255',
            'email'       => 'required|email|unique:users,email',
            'password'    => 'required|string',
            'role'        => ['required', Rule::in(['manager', 'operator'])],

            // IDs des branches à assigner — obligatoire pour manager et operator
            'branche_ids'   => 'required|array|min:1',
            'branche_ids.*' => 'integer|exists:branches,id',
        ];
    }

    public function messages(): array
    {
        return [
            'email.unique'          => 'Cet email est déjà utilisé.',
            'role.in'               => 'Le rôle doit être "manager" ou "operator". Pour créer un admin, utilisez l\'inscription.',
            'branche_ids.required'  => 'Vous devez assigner au moins une branche.',
            'branche_ids.*.exists'  => 'Une des branches sélectionnées n\'existe pas.',
        ];
    }
}
