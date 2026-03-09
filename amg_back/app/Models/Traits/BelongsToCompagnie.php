<?php

namespace App\Models\Traits;

use App\Models\Scopes\CompagnieScope;

trait BelongsToCompagnie
{
    /**
     * Laravel appelle automatiquement boot{TraitName}() au démarrage du modèle.
     * Pas besoin d'appeler quoi que ce soit dans booted() du modèle.
     */
    protected static function bootBelongsToCompagnie()
    {
        // 1. Lecture : filtre toutes les requêtes SELECT par compagnie_id
        static::addGlobalScope(new CompagnieScope);

        // 2. Insertion : injecte compagnie_id automatiquement
        // empty() évite d'écraser un compagnie_id déjà fourni explicitement
        // (ex: AuthController::register qui pose compagnie_id lui-même)
        static::creating(function ($model) {
            if (auth()->check() && empty($model->compagnie_id)) {
                $model->compagnie_id = auth()->user()->compagnie_id;
            }
        });
    }
}
