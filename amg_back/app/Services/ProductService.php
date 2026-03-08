<?php
namespace App\Services;

use App\Models\Product;
use App\Models\Stock;
use Illuminate\Support\Facades\DB;

class ProductService {
    // Creation du produit
    public function createProduct($data){
        return DB::transaction(function() use ($data){
            // Creation du produit
            $product = Product::create([
                "compagnie_id"=>auth()->id(),
                "name"=>$data->name,
                "sku"=>$data->sku,
                "price"=>$data->price,
            ]);

            //Initialiser le product dans la branche
            $stock = Stock::create([
                "product_id"=>$product->id,
                "branche_id"=>$data->branche_id,
                "quantity"=>$data->quantity ?? 0
            ]);
        } );
    }
}
