<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreMovementRequest;
use App\Http\Requests\StoreStockMovementRequest;
use App\Http\Requests\StoreTransfertRequest;
use App\Http\Requests\TransfertStockMovementRequest;
use App\Http\Resources\StockMovementResource;
use App\Models\Branche;
use App\Models\Stock;
use App\Models\StockMovement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StockMovementController extends Controller
{
    /**
     * Eager load sans aucun scope global.
     *
     * POURQUOI withoutGlobalScopes() partout :
     * - Stock      → SoftDeletes ajoute "deleted_at is null"
     * - Product    → CompagnieScope ajoute "WHERE compagnie_id = X"
     * - Branche    → CompagnieScope ajoute "WHERE compagnie_id = X"
     *
     * Sans ça, le eager load filtre silencieusement les relations
     * et retourne null pour certains stocks/branches.
     * Conséquence : la Resource plante ou retourne une collection vide.
     * Le périmètre de sécurité est déjà garanti par le whereHas sur branche_id —
     * pas besoin des scopes en plus ici.
     */
    private function eagerLoad(): array
    {
        return [
            'stock' => fn($q) => $q->withoutGlobalScopes()->with([
                'product' => fn($q2) => $q2->withoutGlobalScopes(),
                'branche' => fn($q3) => $q3->withoutGlobalScopes(),
            ]),
            'user',
        ];
    }

    /**
     * Lister les mouvements accessibles selon le rôle.
     * Filtres optionnels : ?product_id=, ?type=, ?branche_id=
     */
    public function index(Request $request)
    {
        $this->authorize('viewAny', StockMovement::class);

        $user       = auth()->user();
        $brancheIds = $user->accessibleBrancheIds();

        if (empty($brancheIds)) {
            return StockMovementResource::collection(collect([]));
        }

        $movements = StockMovement::with($this->eagerLoad())
            ->whereHas('stock', fn($q) => $q->withoutGlobalScopes()->whereIn('branche_id', $brancheIds))
            ->when($request->product_id, fn($q) => $q->whereHas('stock', fn($q2) =>
                $q2->withoutGlobalScopes()->where('product_id', $request->product_id)
            ))
            ->when($request->type,       fn($q) => $q->where('type', $request->type))
            ->when($request->branche_id, fn($q) => $q->whereHas('stock', fn($q2) =>
                $q2->withoutGlobalScopes()->where('branche_id', $request->branche_id)
            ))
            ->latest()
            ->paginate(50);

        return StockMovementResource::collection($movements);
    }

    /**
     * Créer un mouvement in / out / adjustment.
     *
     * L'utilisateur envoie : product_id + branche_id + type + quantity.
     * Le controller résout le stock lui-même — l'utilisateur n'a pas
     * à connaître les stock_id internes.
     * Si le produit n'a pas encore de stock dans cette branche, on le crée à 0.
     */
    public function store(StoreStockMovementRequest $request)
    {
        $this->authorize('create', StockMovement::class);

        $user = auth()->user();

        // Vérification du périmètre de branche
        if (!$user->canAccessBranche($request->branche_id)) {
            return response()->json([
                'message' => 'Vous n\'avez pas accès à cette branche.',
            ], 403);
        }

        // Résoudre le stock — le crée à 0 s'il n'existe pas encore
        $stock = Stock::withoutGlobalScopes()->firstOrCreate(
            [
                'product_id' => $request->product_id,
                'branche_id' => $request->branche_id,
            ],
            ['quantity' => 0]
        );

        if ($request->type === 'out' && $stock->quantity < $request->quantity) {
            return response()->json([
                'message' => "Stock insuffisant. Disponible : {$stock->quantity}, demandé : {$request->quantity}.",
            ], 422);
        }

        $movement = StockMovement::create([
            'stock_id'  => $stock->id,
            'user_id'   => $user->id,
            'type'      => $request->type,
            'quantity'  => $request->quantity,
            'reference' => StockMovement::generateReference(),
            'reason'    => $request->reason,
        ]);

        return new StockMovementResource(
            $movement->load($this->eagerLoad())
        );
    }

    /**
     * Transfert inter-branches — admin et manager uniquement.
     */
    public function transfert(TransfertStockMovementRequest $request)
    {
        $this->authorize('transfert', StockMovement::class);

        $user = auth()->user();

        $brancheSource = Branche::withoutGlobalScopes()->findOrFail($request->branche_source_id);
        $brancheDest   = Branche::withoutGlobalScopes()->findOrFail($request->branche_dest_id);


        
        if (
            $brancheSource->compagnie_id !== $user->compagnie_id ||
            $brancheDest->compagnie_id   !== $user->compagnie_id
        ) {
            return response()->json([
                'message' => 'Les branches ne font pas partie de votre compagnie.',
            ], 403);
        }

        if (
            !$user->canAccessBranche($brancheSource->id) ||
            !$user->canAccessBranche($brancheDest->id)
        ) {
            return response()->json([
                'message' => 'Vous n\'avez pas accès à l\'une des branches du transfert.',
            ], 403);
        }

        $stockSource = Stock::withoutGlobalScopes()
                            ->where('product_id', $request->product_id)
                            ->where('branche_id', $brancheSource->id)
                            ->first();

        if (!$stockSource) {
            return response()->json([
                'message' => "Le produit n'existe pas dans la branche source ({$brancheSource->name}).",
            ], 422);
        }

        $stockDest = Stock::withoutGlobalScopes()->firstOrCreate(
            ['product_id' => $request->product_id, 'branche_id' => $brancheDest->id],
            ['quantity'   => 0]
        );

        if ($stockSource->quantity < $request->quantity) {
            return response()->json([
                'message' => "Stock source insuffisant. Disponible : {$stockSource->quantity}, demandé : {$request->quantity}.",
            ], 422);
        }

        $reference = StockMovement::generateReference();
        $reason    = $request->reason ?? "Transfert vers : {$brancheDest->name}";

        [$mvtOut, $mvtIn] = DB::transaction(function () use (
            $request, $user, $stockSource, $stockDest, $brancheSource, $brancheDest, $reference, $reason
        ) {
            $mvtOut = StockMovement::create([
                'stock_id'  => $stockSource->id,
                'user_id'   => $user->id,
                'type'      => 'transfert',
                'quantity'  => $request->quantity,
                'reference' => $reference . '-OUT',
                'reason'    => $reason,
            ]);
            $stockSource->decrement('quantity', $request->quantity);

            $mvtIn = StockMovement::create([
                'stock_id'  => $stockDest->id,
                'user_id'   => $user->id,
                'type'      => 'transfert',
                'quantity'  => $request->quantity,
                'reference' => $reference . '-IN',
                'reason'    => "Transfert depuis : {$brancheSource->name}",
            ]);
            $stockDest->increment('quantity', $request->quantity);

            return [$mvtOut, $mvtIn];
        });

        return response()->json([
            'message'       => 'Transfert effectué avec succès.',
            'mouvement_out' => new StockMovementResource($mvtOut->load($this->eagerLoad())),
            'mouvement_in'  => new StockMovementResource($mvtIn->load($this->eagerLoad())),
        ], 201);
    }

    /**
     * Voir un mouvement — vérifié via policy.
     */
    public function show(StockMovement $stockMovement)
    {
        $stockMovement->load($this->eagerLoad());
        $this->authorize('view', $stockMovement);
        return new StockMovementResource($stockMovement);
    }
}
