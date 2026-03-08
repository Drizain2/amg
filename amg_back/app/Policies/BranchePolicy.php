<?php

namespace App\Policies;

use App\Models\Branche;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class BranchePolicy
{
    /**
     * L'admin peut tout faire sur les branches de sa compagnie.
     * Cette méthode "before" court-circuite toutes les autres si elle retourne true.
     */
    public function before(User $user, string $ability): ?bool
    {
        if($user->isAdmin()) {
            return true; // L'admin a tous les droits sur les branches de sa compagnie
        }
        return null; // Continuer à vérifier les autres méthodes pour manager et operator
    }

    /**
     * Lister les branches.
     * Manager et operator voient uniquement leurs branches assignées.
     */
    public function viewAny(User $user): bool
    {
// Tout le monde peut lister (filtré dans le controller)
        return true;
    }

        /**
     * Voir / utiliser une branche spécifique.
     */
    public function view(User $user, Branche $branche): bool
    {

        return $user->canAccessBranche($branche->id);
    }

    /**
     * Créer une branche : réservé à l'admin (géré par before()).
     */
    public function create(User $user): bool
    {
        // Non-admins bloqués ici, admins passent par before()
        return false;
    }

    /**
     * Modifier une branche : réservé à l'admin (géré par before()).
     */
    public function update(User $user, Branche $branche): bool
    {
        return false;
    }

    /**
     * Supprimer une branche : réservé à l'admin (géré par before()).
     */
    public function delete(User $user, Branche $branche): bool
    {
        return false;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Branche $branche): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Branche $branche): bool
    {
        return false;
    }
}
