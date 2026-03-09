<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreUserRequest;
use App\Http\Resources\UserResource;
use App\Models\Branche;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{
    /**
     * Lister tous les utilisateurs de la compagnie.
     * Réservé à l'admin.
     */
    public function index()
    {
        $this->authorize('viewAny', User::class);

        $user = User::with('branches')->get();

        return UserResource::collection($user);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Inviter un nouvel utilisateur dans la compagnie.
     * Réservé à l'admin.
     *
     * Logique :
     * 1. Créer le user avec le rôle choisi (manager ou operator)
     * 2. Vérifier que les branches appartiennent bien à la compagnie de l'admin
     * 3. Attacher les branches via la pivot branche_user
     */
    public function store(StoreUserRequest $request)
    {
        // Vérifier que toutes les branches fournies appartiennent à la compagnie de l'admin
        // C'est une vérification critique : un admin ne doit pas pouvoir
        // assigner des branches d'une autre compagnie
        $brancheIds = $request->input('branche_ids');
        $compagnieId = auth()->user()->compagnie_id;
        $branchesValides = Branche::whereIn('id', $brancheIds)
            ->where('compagnie_id', $compagnieId)
            ->pluck('id');

        if ($branchesValides->count() !== count($brancheIds)) {
            return response()->json([
                'message' => 'Une ou plusieurs branches ne font pas partie de votre compagnie.'
            ], 422);
        }

        $user = DB::transaction(function () use ($request, $compagnieId, $branchesValides) {
            $user = User::create([
                'name' => $request->name,
                'last_name' => $request->last_name,
                'email' => $request->email,
                'password' => $request->password,
                'role' => $request->role,
                'compagnie_id' => $compagnieId,
            ]);

            // Attacher les branches via la pivot
            $user->branches()->attach($branchesValides);

            return $user;
        });

        return new UserResource($user->load('branches'));
    }

    /**
     * Afficher un utilisateur avec ses branches.
     * Réservé à l'admin.
     */
    public function show(User $user_compagnie)
    {
        $this->authorize('view', $user_compagnie);

        return new UserResource($user_compagnie->load('branches'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $user)
    {
        //
    }

    /**
     * Modifier le rôle et/ou les branches d'un utilisateur.
     * Réservé à l'admin.
     *
     * Pour les branches : on utilise sync() qui remplace entièrement
     * les branches assignées — plus simple et sans risque d'incohérence.
     */
    public function update(Request $request, User $user_compagnie)
    {
        $this->authorize('update', $user_compagnie);

        $compagnieId = auth()->user()->compagnie_id;

        DB::transaction(function () use ($request, $user_compagnie, $compagnieId) {

            $user_compagnie->update($request->only('name', 'last_name', 'role'));

            if ($request->has('branche_ids')) {
                $branchesValides = Branche::whereIn('id', $request->branche_ids)
                    ->where('compagnie_id', $compagnieId)
                    ->pluck('id');

                if ($branchesValides->count() !== count($request->branche_ids)) {
                    abort(422, 'Une ou plusieurs branches ne font pas partie de votre compagnie.');
                }

                $user_compagnie->branches()->sync($branchesValides);
            }
        });
        return new UserResource($user_compagnie->load('branches'));
    }

    /**
     * Retirer un utilisateur de la compagnie (suppression soft si disponible).
     * Réservé à l'admin.
     * L'admin ne peut pas se supprimer lui-même, ni supprimer un autre admin.
     */
    public function destroy(User $user_compagnie)
    {
        $this->authorize('delete', $user_compagnie);

        // Détacher toutes ses branches avant suppression
        $user_compagnie->branches()->detach();
        $user_compagnie->delete();

        return response()->json(['message' => 'Utilisateur retiré avec succès.']);
    }
}
