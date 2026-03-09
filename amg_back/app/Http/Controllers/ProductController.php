<?php

namespace App\Http\Controllers;

use App\Models\Branche;
use App\Models\Product;
use App\Models\Stock;
use App\Models\StockMovement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProductController extends Controller
{
    /**
     * Lister les produits de la compagnie.
     * Le CompagnieScope (via trait BelongsToCompagnie) filtre automatiquement.
     */
    public function index()
    {
        return Product::with('stocks.branche')->get();
    }

    /**
     * Créer un produit et initialiser son stock dans une branche.
     */
    public function store(Request $request)
    {
        // $this->authorize('create', $branche);
        $request->validate([

            "name" => "required|string",
            "sku" => "required|string|unique:products,sku",
            "price" => "required|numeric|min:0",
            "branche_id" => "required|exists:branches,id",
            "initial_quantity" => "nullable|integer|min:0"
        ]);
        $branche = Branche::findOrFail($request->branche_id);
        $this->authorize('view', $branche);

        // Utilisation d'une transaction pour garantir la création du produit et du stock
        return DB::transaction(function () use ($request) {
            // Création du produit — compagnie_id injecté par BelongsToCompagnie
            $product = Product::create([
                "name" => $request->name,
                "sku" => $request->sku,
                "price" => $request->price,
            ]);

            // Initialisation du stock dans la branche (quantité à 0 par défaut)
            $stock = Stock::create([
                "product_id" => $product->id,
                "branche_id" => $request->branche_id,
                "quantity" => 0
                // On pose 0 ici volontairement : c'est le StockMovement qui incrémente
                // via l'observer, évitant tout double comptage
            ]);

            // Mouvement initial si une quantité est fournie
            if ($request->initial_quantity > 0) {
                StockMovement::create([
                    "reference" => StockMovement::generateReference(),
                    "stock_id" => $stock->id,
                    "user_id" => auth()->id(),
                    "type" => "in",
                    "quantity" => $request->initial_quantity,
                    "reason" => "Stock initial à la création du produit"
                ]);
                // L'observer incrémente stock.quantity automatiquement
            }

            return response()->json([
                "message" => "Produit et stock initialisés.",
                "product" => $product->load('stocks.branche')
            ], 201);
        });

    }

    /**
     * Display the specified resource.
     */
    public function show(Product $product)
    {
        return $product->load("stocks.branche");
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Product $product)
    {
        $request->validate([
            "name" => "sometimes|required|string",
            "price" => "sometimes|required|numeric|min:0",
        ]);

        $product->update($request->only('name', 'price'));

        return $product->load('stocks.branche');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product)
    {
        // Soft delete — le stock et les mouvements sont conservés pour l'historique
        $product->delete();

        return response()->json(['message' => 'Produit archivé avec succès.']);
    }
}
