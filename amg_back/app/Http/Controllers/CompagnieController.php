<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateCompagnieRequest;
use App\Http\Resources\CompagnieResource;
use App\Models\Compagnie;
use Illuminate\Support\Str;

class CompagnieController extends Controller
{
    /**
     * Voir les infos de sa propre compagnie.
     * Accessible à tous les rôles.
     *
     * On n'utilise pas le route model binding standard ici —
     * chaque user ne peut voir que SA compagnie, donc on la
     * résout directement depuis auth() sans passer d'ID dans l'URL.
     * Plus simple et plus sûr : impossible d'accéder à une autre compagnie.
     */
    public function show()
    {
        $compagnie = Compagnie::withCount(['branches', 'users', 'products'])
                              ->findOrFail(auth()->user()->compagnie_id);

        $this->authorize('view', $compagnie);

        return new CompagnieResource($compagnie);
    }

    /**
     * Modifier les infos de sa compagnie — admin uniquement.
     * Même logique : on résout la compagnie depuis auth(), pas depuis l'URL.
     */
    public function update(UpdateCompagnieRequest $request)
    {
        $compagnie = Compagnie::findOrFail(auth()->user()->compagnie_id);

        $this->authorize('update', $compagnie);

        $data = $request->validated();

        // Si le nom change, on met à jour le slug aussi
        if (isset($data['name'])) {
            $data['slug'] = Str::slug($data['name']);
        }

        $compagnie->update($data);

        return new CompagnieResource(
            $compagnie->loadCount(['branches', 'users', 'products'])
        );
    }
}
