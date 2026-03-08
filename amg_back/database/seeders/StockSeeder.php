<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\Stock;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class StockSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('📊 Création des stocks initiaux...');

        $products = Product::query()->with('compagnie.branches')->get();
        $superAdmin = User::where('email', 'superadmin@platform.com')->first();

        foreach ($products as $product) {
            $branches = $product->compagnie->branches;

            foreach ($branches as $branch) {
                // Quantité initiale aléatoire selon le type de produit
                $quantity = $this->getInitialQuantity($product);

                // Créer le stock
                $stock = Stock::create([
                    'product_id' => $product->id,
                    'branche_id' => $branch->id,
                    'quantity' => $quantity,
                    // 'reserved_quantity' => 0,
                    // 'available_quantity' => $quantity,
                ]);

                // Créer le mouvement initial
                // StockMovement::create([
                //     'reference' => StockMovement::generateReference(),
                //     'type' => StockMovement::TYPE_INITIAL,
                //     'quantity' => $quantity,
                //     'quantity_before' => 0,
                //     'quantity_after' => $quantity,
                //     'unit_cost' => $product->cost_price,
                //     'notes' => "Stock initial - {$product->name}",
                //     'product_id' => $product->id,
                //     'branch_id' => $branch->id,
                //     'user_id' => $superAdmin->id,
                // ]);
            }

            $this->command->info("   ✓ Stocks créés pour : {$product->name}");
        }
    }
     private function getInitialQuantity(Product $product): int
    {
        // Produits chers (> 500k) : faible stock
        if ($product->selling_price > 500000) {
            return rand(2, 8);
        }

        // Produits moyens (100k - 500k) : stock moyen
        if ($product->selling_price > 100000) {
            return rand(10, 30);
        }

        // Accessoires et petits produits : stock élevé
        return rand(30, 100);
    }
}
