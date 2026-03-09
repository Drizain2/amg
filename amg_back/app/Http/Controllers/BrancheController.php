<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreBrancheRequest;
use App\Http\Requests\UpdateBrancheRequest;
use App\Http\Resources\BrancheResource;
use App\Models\Branche;

class BrancheController extends Controller
{
    /**
     * Lister les branches accessibles selon le rôle.
     *
     * - Admin    → toutes les branches de sa compagnie
     * - Manager  → uniquement ses branches assignées (pivot)
     * - Operator → uniquement sa branche assignée (pivot)
     */
    public function index()
    {
        $user = auth()->user();

        //Admin
        if ($user->isAdmin()) {
           $branches = Branche::where('compagnie_id', $user->compagnie_id)
                               ->withCount('stocks')
                               ->get();
        } else {
            // Manager et Operator
            $branches = $user->branches()->withCount('stocks')->get();
        }
        return BrancheResource::collection($branches);
    }

    /**
     * Créer une nouvelle branche.
     * Réservé à l'admin (géré dans StoreBrancheRequest::authorize()).
     */
    public function store(StoreBrancheRequest $request)
    {
        $branche = Branche::create($request->validated());

        return new BrancheResource($branche);
    }

    /**
     * Afficher une branche spécifique avec ses users assignés.
     * Vérifie que le user a accès à cette branche via la policy.
     */
    public function show(Branche $branche)
    {
        $this->authorize('view', $branche);

        $branche->loadCount('stocks')->load('users');

        return new BrancheResource($branche);
    }

    /**
     * Modifier une branche.
     * Réservé à l'admin (géré dans UpdateBrancheRequest::authorize()).
     * On vérifie aussi que la branche appartient bien à la compagnie de l'admin.
     */
    public function update(UpdateBrancheRequest $request, Branche $branche)
    {
        // Double vérification : l'admin ne peut modifier que ses propres branches
        $this->authorize('update', $branche);

        $branche->update($request->validated());

        return new BrancheResource($branche);
    }

    /**
     * Supprimer une branche (soft delete).
     * Réservé à l'admin.
     * On empêche la suppression de la dernière branche.
     */
    public function destroy(Branche $branche)
    {
        $this->authorize('delete', $branche);

                $totalBranches = Branche::where('compagnie_id', auth()->user()->compagnie_id)->count();

        if ($totalBranches <= 1) {
            return response()->json([
                'message' => 'Impossible de supprimer la dernière branche de la compagnie.'
            ], 422);
        }

        $branche->delete();

        return response()->json(['message' => 'Branche supprimée avec succès.']);
    }
}
