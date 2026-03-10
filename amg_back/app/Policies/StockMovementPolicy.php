<?php

namespace App\Policies;

use App\Models\StockMovement;
use App\Models\User;

class StockMovementPolicy
{
    /**
     * Voir l'historique des mouvements.
     * Tout le monde peut lister — le controller filtre selon le périmètre.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Voir un mouvement spécifique.
     * Accessible si la branche du stock est dans le périmètre du user.
     */
    public function view(User $user, StockMovement $movement): bool
    {
        $brancheId = $movement->stock?->branche_id;

        if (!$brancheId) return false;

        return $user->canAccessBranche($brancheId);
    }

    /**
     * Créer un mouvement in / out / adjustment.
     * Tous les rôles peuvent créer — la vérification du périmètre
     * de branche est faite dans le controller.
     */
    public function create(User $user): bool
    {
        return true;
    }

    /**
     * Effectuer un transfert inter-branches.
     * Réservé à l'admin et au manager — l'operator n'agit que sur son dépôt.
     */
    public function transfert(User $user): bool
    {
        return $user->isAdmin() || $user->isManager();
    }
}
