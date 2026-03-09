<?php

namespace Database\Seeders;

use App\Models\Branche;
use App\Models\Compagnie;
use App\Models\Product;
use App\Models\Stock;
use App\Models\StockMovement;
use App\Models\User;
use Illuminate\Database\Seeder;

class StockSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('📊 Création des stocks et mouvements initiaux...');

        $compagnies = Compagnie::all();

        foreach ($compagnies as $compagnie) {
            // Bypass scopes — on est hors auth
            $branches = Branche::withoutGlobalScopes()
                ->where('compagnie_id', $compagnie->id)
                ->get();

            $products = Product::withoutGlobalScopes()
                ->where('compagnie_id', $compagnie->id)
                ->get();

            // Admin de la compagnie — utilisé comme auteur des mouvements initiaux
            $admin = User::where('compagnie_id', $compagnie->id)
                ->where('role', 'admin')
                ->first();

            if (!$admin || $branches->isEmpty() || $products->isEmpty()) {
                $this->command->warn("   ⚠ Données manquantes pour : {$compagnie->name}");
                continue;
            }

            foreach ($products as $product) {
                foreach ($branches as $branche) {
                    $quantite = $this->quantiteInitiale($product->price);

                    // 1. Créer la ligne de stock à 0
                    // C'est le StockMovement qui va incrémenter via l'observer
                    $stock = Stock::create([
                        'product_id' => $product->id,
                        'branche_id' => $branche->id,
                        'quantity' => 0,
                    ]);

                    // 2. Créer le mouvement initial "in"
                    // L'observer StockMovementObserver@created va automatiquement
                    // incrémenter stock.quantity du bon montant
                    StockMovement::create([
                        'stock_id' => $stock->id,
                        'user_id' => $admin->id,
                        'type' => 'in',
                        'quantity' => $quantite,
                        'reference' => StockMovement::generateReference(),
                        'reason' => 'Stock initial — mise en place',
                    ]);
                }
            }

            $this->command->info("   ✓ {$compagnie->name} → stocks initialisés");
        }

        $this->command->newLine();
        $this->command->info('🔄 Création de mouvements de test supplémentaires...');
        $this->creerMouvementsDeTest();
    }

    /**
     * Crée des mouvements supplémentaires variés (out, adjustment, transfert)
     * pour avoir un historique réaliste à tester.
     */
    private function creerMouvementsDeTest(): void
    {
        // On travaille uniquement sur AMG Global pour les mouvements de test
        $compagnie = Compagnie::where('name', 'AMG Global')->first();
        if (!$compagnie)
            return;

        $admin = User::where('compagnie_id', $compagnie->id)->where('role', 'admin')->first();
        $manager = User::where('compagnie_id', $compagnie->id)->where('role', 'manager')->first();
        $operator = User::where('compagnie_id', $compagnie->id)->where('role', 'operator')->first();

        if (!$admin || !$manager || !$operator)
            return;

        // Quelques sorties de stock (simule des ventes)
        $stocks = Stock::withoutGlobalScopes()
            ->whereHas('branche', fn($q) => $q->where('compagnie_id', $compagnie->id))
            ->with('branche')
            ->take(6)
            ->get();

        foreach ($stocks as $stock) {
            if ($stock->quantity < 3)
                continue;

            StockMovement::create([
                'stock_id' => $stock->id,
                'user_id' => $operator->id,
                'type' => 'out',
                'quantity' => rand(1, min(3, $stock->quantity)),
                'reference' => StockMovement::generateReference(),
                'reason' => 'Vente client',
            ]);
        }

        // Un ajustement d'inventaire
        $stockAjust = Stock::withoutGlobalScopes()
            ->whereHas('branche', fn($q) => $q->where('compagnie_id', $compagnie->id))
            ->first();

        if ($stockAjust) {
            StockMovement::create([
                'stock_id' => $stockAjust->id,
                'user_id' => $manager->id,
                'type' => 'adjustment',
                'quantity' => 5,
                'reference' => StockMovement::generateReference(),
                'reason' => 'Correction inventaire mensuel',
            ]);
        }

        // Un transfert entre la première et la deuxième branche
        $branches = Branche::withoutGlobalScopes()
            ->where('compagnie_id', $compagnie->id)
            ->get();

        if ($branches->count() >= 2) {
            $branche1 = $branches->first();
            $branche2 = $branches->get(1);

            // Trouver un produit présent dans les deux branches
            $stockSource = Stock::withoutGlobalScopes()
                ->where('branche_id', $branche1->id)
                ->where('quantity', '>=', 3)
                ->first();

            $stockDest = Stock::withoutGlobalScopes()
                ->where('branche_id', $branche2->id)
                ->where('product_id', $stockSource?->product_id)
                ->first();

            if ($stockSource && $stockDest) {
                $ref = StockMovement::generateReference();

                // OUT sur la source
                StockMovement::create([
                    'stock_id' => $stockSource->id,
                    'user_id' => $admin->id,
                    'type' => 'transfert',
                    'quantity' => 2,
                    'reference' => $ref . '-OUT',
                    'reason' => "Transfert vers : {$branche2->name}",
                ]);
                $stockSource->decrement('quantity', 2);

                // IN sur la destination
                StockMovement::create([
                    'stock_id' => $stockDest->id,
                    'user_id' => $admin->id,
                    'type' => 'transfert',
                    'quantity' => 2,
                    'reference' => $ref . '-IN',
                    'reason' => "Transfert depuis : {$branche1->name}",
                ]);
                $stockDest->increment('quantity', 2);

                $this->command->info("   ✓ Transfert test créé : {$branche1->name} → {$branche2->name}");
            }
        }
    }

    /**
     * Quantité initiale selon le prix du produit.
     */
    private function quantiteInitiale(float $price): int
    {
        if ($price >= 500000)
            return rand(2, 5);    // Produits premium
        if ($price >= 100000)
            return rand(5, 15);   // Produits milieu de gamme
        if ($price >= 20000)
            return rand(15, 40);  // Produits courants
        return rand(40, 100);                        // Accessoires / consommables
    }
}