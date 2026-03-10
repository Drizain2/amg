<?php

namespace App\Policies;

use App\Models\Compagnie;
use App\Models\User;

class CompagniePolicy
{
    /**
     * Voir les infos de la compagnie.
     * Tout utilisateur connecté peut voir sa propre compagnie.
     */
    public function view(User $user, Compagnie $compagnie): bool
    {
        return $user->compagnie_id === $compagnie->id;
    }

    /**
     * Modifier les infos de la compagnie — admin uniquement.
     */
    public function update(User $user, Compagnie $compagnie): bool
    {
        return $user->isAdmin() && $user->compagnie_id === $compagnie->id;
    }
}
