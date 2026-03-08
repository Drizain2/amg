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
     * Lister uniquement les Produits de Ma société.
     */
    public function index()
    {
        return Product::with('stocks')->get();
        return Product::where('compagnie_id', auth()->user()->compagnie_id)->get();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            "name" => "required|string",
            "sku" => "required|string|unique:products,sku",
            "price" => "required|numeric",
            "branche_id" => "required|exists:branches,id",
            "initial_quantity" => "nullable|integer|min:0"
        ]);
        $branche =Branche::findOrFail($request->branche_id);
        $this->authorize('view',$branche);

        // Utilisation d'une transaction pour garantir la création du produit et du stock
        return DB::transaction(function () use ($request) {
            // Creation du produit
            $product = Product::create([
                "compagnie_id" => auth()->user()->compagnie_id,
                "name" => $request->name,
                "sku" => $request->sku,
                "price" => $request->price,
            ]);

            //Initialiser le product dans la branche
            $stock = Stock::create([
                "product_id" => $product->id,
                "branche_id" => $request->branche_id,
                "quantity" => $request->quantity ?? 0
            ]);

            // Mouvement
            if ($request->quantity > 0) {
                StockMovement::create([
                    "reference"=>$product->name . date("YYYY/MM/DD"),
                    "stock_id" => $stock->id,
                    "user_id" => auth()->id(),
                    "type" => "in",
                    "quantity" => $request->quantity,
                    "reason" => "Stock initial à la création du produit"
                ]);
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
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Product $product)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product)
    {
        //
    }
}
