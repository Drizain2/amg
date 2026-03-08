<?php

namespace App\Models\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

class CompagnieScope implements Scope
{
    /**
     * Apply the scope to a given Eloquent query builder.
     */
    public function apply(Builder $builder, Model $model): void
    {
       //Verifier si l'utilisateur est authentifié et filtrer par company_id
        if (auth()->check()) {
            $builder->where('compagnie_id', auth()->user()->compagnie_id);
        }
    }
}
