<?php

namespace Database\Seeders;

use App\Models\Compagnie;
use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    // Catalogue commun — chaque compagnie aura un sous-ensemble
    private array $catalogue = [
        // Ordinateurs
        ['name' => 'Dell Latitude E7470',       'sku' => 'DELL-E7470',      'price' => 280000],
        ['name' => 'HP ProBook 450 G8',          'sku' => 'HP-PB450G8',      'price' => 350000],
        ['name' => 'Lenovo ThinkPad T14',        'sku' => 'LEN-T14',         'price' => 320000],
        ['name' => 'MacBook Air M2',             'sku' => 'APPLE-MBA-M2',    'price' => 850000],

        // Smartphones
        ['name' => 'iPhone 15 128GB',            'sku' => 'IPH15-128',       'price' => 550000],
        ['name' => 'Samsung Galaxy A54',         'sku' => 'SAM-A54',         'price' => 180000],
        ['name' => 'Tecno Spark 20',             'sku' => 'TEC-SP20',        'price' => 75000],
        ['name' => 'Infinix Hot 40',             'sku' => 'INF-HOT40',       'price' => 65000],

        // Accessoires
        ['name' => 'Souris Logitech M185',       'sku' => 'LOG-M185',        'price' => 8000],
        ['name' => 'Clavier Logitech K120',      'sku' => 'LOG-K120',        'price' => 12000],
        ['name' => 'Écran HP 22" FHD',           'sku' => 'HP-MON22',        'price' => 95000],
        ['name' => 'Câble HDMI 2m',              'sku' => 'HDMI-2M',         'price' => 3500],
        ['name' => 'Chargeur USB-C 65W',         'sku' => 'CHG-USBC-65W',    'price' => 15000],
        ['name' => 'Sac à dos PC 15"',           'sku' => 'SAC-PC-15',       'price' => 18000],

        // Réseau
        ['name' => 'Routeur TP-Link AX1800',     'sku' => 'TPL-AX1800',      'price' => 55000],
        ['name' => 'Switch 8 ports Gigabit',     'sku' => 'SWI-8P-GIG',      'price' => 22000],
        ['name' => 'Câble RJ45 Cat6 - 10m',      'sku' => 'RJ45-CAT6-10M',   'price' => 4500],

        // Imprimantes
        ['name' => 'Imprimante HP LaserJet 107a','sku' => 'HP-LJ107A',       'price' => 85000],
        ['name' => 'Cartouche HP 85A',           'sku' => 'HP-CART85A',      'price' => 25000],

        // Stockage
        ['name' => 'Disque dur externe 1TB',     'sku' => 'HDD-EXT-1TB',     'price' => 35000],
        ['name' => 'Clé USB 64GB Kingston',      'sku' => 'USB-64G-KIN',     'price' => 6000],
        ['name' => 'SSD Samsung 500GB',          'sku' => 'SSD-SAM-500G',    'price' => 48000],
    ];

    public function run(): void
    {
        $this->command->info('📦 Création des produits...');

        $compagnies = Compagnie::all();

        foreach ($compagnies as $compagnie) {
            // Chaque compagnie reçoit 12 produits aléatoires du catalogue
            // withoutGlobalScopes non nécessaire ici car on crée sans auth —
            // le hook creating du BelongsToCompagnie est désactivé si auth()->check() = false,
            // donc on passe compagnie_id explicitement
            $selection = collect($this->catalogue)->shuffle()->take(12);

            foreach ($selection as $data) {
                // Le SKU doit être unique globalement — on suffixe par l'id compagnie
                Product::withoutGlobalScopes()->create([
                    'compagnie_id' => $compagnie->id,
                    'name'         => $data['name'],
                    'sku'          => $data['sku'] . '-C' . $compagnie->id,
                    'price'        => $data['price'],
                ]);
            }

            $this->command->info("   ✓ {$compagnie->name} → 12 produits créés");
        }
    }
}