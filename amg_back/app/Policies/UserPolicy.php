<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\Response;

class UserPolicy
{
   /**
     * L'admin passe partout — mais uniquement sur les users de SA compagnie.
     * On retourne null pour les non-admins afin que les méthodes spécifiques décident.
     */

    public function before(User $currentUser, string $ability): ?bool
    {
        if ($currentUser->isAdmin()) {
            return true;
        }
        return null;
    }

    /**
     * Lister les users : admin uniquement (géré par before()).
     */
    public function viewAny(User $user): bool
    {
        return false;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, User $model): bool
    {
        return false;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return false;
    }

     /**
     * Modifier un user : admin uniquement.
     * L'admin ne peut modifier que les users de sa propre compagnie.
     * On empêche aussi l'admin de modifier un autre admin.
     */
    public function update(User $currentUser, User $targetUser): bool
    {
        // Ne pas modifier un admin (sécurité : éviter l'escalade de privilèges)
        if ($targetUser->isAdmin()) {
            return false;
        }

        return $currentUser->compagnie_id === $targetUser->compagnie_id;
    }

     /**
     * Supprimer un user : admin uniquement.
     * L'admin ne peut pas se supprimer lui-même ni supprimer un autre admin.
     */
    public function delete(User $currentUser, User $targetUser): bool
    {
        if ($targetUser->isAdmin()) {
            return false;
        }

        // L'admin ne peut pas se supprimer lui-même
        if ($currentUser->id === $targetUser->id) {
            return false;
        }

        return $currentUser->compagnie_id === $targetUser->compagnie_id;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, User $model): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, User $model): bool
    {
        return false;
    }
}
