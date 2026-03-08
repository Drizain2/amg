<?php

namespace Database\Seeders;

use App\Models\Compagnie;
use App\Models\Product;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('📦 Création des produits...');

        $companies = Compagnie::all();

        $productsData = [
            // Ordinateurs Portables
            [
                'name' => 'Dell Latitude E6440',
                'sku' => 'DELL-E6440',
                // 'description' => 'PC portable professionnel Intel Core i5',
                'price' => 180000,
                // 'selling_price' => 250000,
                // 'tax_rate' => 18,
                // 'alert_quantity' => 5,
                // 'unit' => 'PIECE',
                // 'category' => 'laptops',
            ],
            [
                'name' => 'HP ProBook 450 G7',
                'sku' => 'HP-PB450G7',
                // 'description' => 'Laptop HP Intel Core i7, 8GB RAM',
                'price' => 350000,
                // 'selling_price' => 480000,
                // 'tax_rate' => 18,
                // 'alert_quantity' => 3,
                // 'unit' => 'PIECE',
                // 'category' => 'laptops',
            ],
            [
                'name' => 'Lenovo ThinkPad T470',
                'sku' => 'LEN-T470',
                // 'description' => 'ThinkPad professionnel robuste',
                'price' => 280000,
                // 'selling_price' => 380000,
                // 'tax_rate' => 18,
                // 'alert_quantity' => 4,
                // 'unit' => 'PIECE',
                // 'category' => 'laptops',
            ],

            // Smartphones
            [
                'name' => 'iPhone 15 Pro 256GB',
                'sku' => 'IPH15-PRO-256',
                // 'description' => 'iPhone 15 Pro Titanium 256GB',
                'price' => 650000,
                // 'selling_price' => 850000,
                // 'tax_rate' => 18,
                // 'alert_quantity' => 2,
                // 'unit' => 'PIECE',
                // 'category' => 'smartphones',
            ],
            [
                'name' => 'Samsung Galaxy S24 Ultra',
                'sku' => 'SAM-S24U-512',
                // 'description' => 'Galaxy S24 Ultra 512GB',
                'price' => 550000,
                // 'selling_price' => 720000,
                // 'tax_rate' => 18,
                // 'alert_quantity' => 3,
                // 'unit' => 'PIECE',
                // 'category' => 'smartphones',
            ],
            [
                'name' => 'Xiaomi Redmi Note 13 Pro',
                'sku' => 'XIA-RN13PRO',
                // 'description' => 'Redmi Note 13 Pro 256GB',
                'price' => 120000,
                // 'selling_price' => 165000,
                // 'tax_rate' => 18,
                // 'alert_quantity' => 10,
                // 'unit' => 'PIECE',
                // 'category' => 'smartphones',
            ],
            [
                'name' => 'POCO X6 Pro',
                'sku' => 'POCO-X6PRO',
                // 'description' => 'POCO X6 Pro 512GB',
                'price' => 180000,
                // 'selling_price' => 240000,
                // 'tax_rate' => 18,
                // 'alert_quantity' => 8,
                // 'unit' => 'PIECE',
                // 'category' => 'smartphones',
            ],
            [
                'name' => 'Infinix Hot 40 Pro',
                'sku' => 'INF-HOT40PRO',
                // 'description' => 'Infinix Hot 40 Pro 256GB',
                'price' => 95000,
                // 'selling_price' => 135000,
                // 'tax_rate' => 18,
                // 'alert_quantity' => 15,
                // 'unit' => 'PIECE',
                // 'category' => 'smartphones',
            ],

            // Tablettes
            [
                'name' => 'iPad Air M2 256GB',
                'sku' => 'IPAD-AIR-M2',
                // 'description' => 'iPad Air avec puce M2',
                'price' => 420000,
                // 'selling_price' => 550000,
                // 'tax_rate' => 18,
                // 'alert_quantity' => 3,
                // 'unit' => 'PIECE',
                // 'category' => 'tablets',
            ],
            [
                'name' => 'Samsung Galaxy Tab S9',
                'sku' => 'SAM-TABS9',
                // 'description' => 'Galaxy Tab S9 128GB',
                'price' => 280000,
                // 'selling_price' => 380000,
                // 'tax_rate' => 18,
                // 'alert_quantity' => 5,
                // 'unit' => 'PIECE',
                // 'category' => 'tablets',
            ],

            // Composants PC
            [
                'name' => 'SSD Samsung 1TB NVMe',
                'sku' => 'SSD-SAM-1TB',
                // 'description' => 'SSD M.2 NVMe 1To haute vitesse',
                'price' => 45000,
                // 'selling_price' => 65000,
                // 'tax_rate' => 18,
                // 'alert_quantity' => 20,
                // 'unit' => 'PIECE',
                // 'category' => 'components',
            ],
            [
                'name' => 'RAM DDR4 16GB',
                'sku' => 'RAM-DDR4-16',
                // 'description' => 'Barrette mémoire DDR4 16GB 3200MHz',
                'price' => 28000,
                // 'selling_price' => 42000,
                // 'tax_rate' => 18,
                // 'alert_quantity' => 25,
                // 'unit' => 'PIECE',
                // 'category' => 'components',
            ],
            [
                'name' => 'Disque Dur 2TB Seagate',
                'sku' => 'HDD-SEA-2TB',
                // 'description' => 'HDD interne 2To 7200RPM',
                'price' => 35000,
                // 'selling_price' => 50000,
                // 'tax_rate' => 18,
                // 'alert_quantity' => 15,
                // 'unit' => 'PIECE',
                // 'category' => 'components',
            ],

            // Périphériques
            [
                'name' => 'Écran Dell 24" Full HD',
                'sku' => 'MON-DELL-24',
                // 'description' => 'Moniteur LED 24 pouces 1920x1080',
                'price' => 85000,
                // 'selling_price' => 125000,
                // 'tax_rate' => 18,
                // 'alert_quantity' => 8,
                // 'unit' => 'PIECE',
                // 'category' => 'peripherals',
            ],
            [
                'name' => 'Clavier Logitech MX Keys',
                'sku' => 'KEY-LOG-MX',
                // 'description' => 'Clavier sans fil rétroéclairé',
                'price' => 42000,
                // 'selling_price' => 60000,
                // 'tax_rate' => 18,
                // 'alert_quantity' => 12,
                // 'unit' => 'PIECE',
                // 'category' => 'peripherals',
            ],
            [
                'name' => 'Souris Logitech MX Master 3',
                'sku' => 'MOU-LOG-MX3',
                // 'description' => 'Souris ergonomique sans fil',
                'price' => 35000,
                // 'selling_price' => 52000,
                // 'tax_rate' => 18,
                // 'alert_quantity' => 15,
                // 'unit' => 'PIECE',
                // 'category' => 'peripherals',
            ],

            // Accessoires
            [
                'name' => 'Chargeur USB-C 65W',
                'sku' => 'CHG-USBC-65',
                // 'description' => 'Chargeur rapide USB-C 65W universel',
                'price' => 8000,
                // 'selling_price' => 15000,
                // 'tax_rate' => 18,
                // 'alert_quantity' => 50,
                // 'unit' => 'PIECE',
                // 'category' => 'accessories',
            ],
            [
                'name' => 'Câble USB-C vers USB-C 2m',
                'sku' => 'CAB-USBC-2M',
                // 'description' => 'Câble charge rapide 100W',
                'price' => 2500,
                // 'selling_price' => 5000,
                // 'tax_rate' => 18,
                // 'alert_quantity' => 100,
                // 'unit' => 'PIECE',
                // 'category' => 'accessories',
            ],
            [
                'name' => 'Housse Laptop 15.6"',
                'sku' => 'HOU-LAP-156',
                // 'description' => 'Housse protection PC portable',
                'price' => 5000,
                // 'selling_price' => 12000,
                // 'tax_rate' => 18,
                // 'alert_quantity' => 30,
                // 'unit' => 'PIECE',
                // 'category' => 'accessories',
            ],

            // Audio
            [
                'name' => 'AirPods Pro 2',
                'sku' => 'AIR-PRO2',
                // 'description' => 'Écouteurs Apple avec réduction de bruit',
                'price' => 120000,
                // 'selling_price' => 165000,
                // 'tax_rate' => 18,
                // 'alert_quantity' => 10,
                // 'unit' => 'PIECE',
                // 'category' => 'audio',
            ],
            [
                'name' => 'Casque Sony WH-1000XM5',
                'sku' => 'HEAD-SONY-XM5',
                // 'description' => 'Casque Bluetooth ANC premium',
                'price' => 145000,
                // 'selling_price' => 195000,
                // 'tax_rate' => 18,
                // 'alert_quantity' => 6,
                // 'unit' => 'PIECE',
                // 'category' => 'audio',
            ],

            // Réseau
            [
                'name' => 'Routeur TP-Link AX3000',
                'sku' => 'ROU-TPL-AX3000',
                // 'description' => 'Routeur WiFi 6 double bande',
                'price' => 65000,
                // 'selling_price' => 95000,
                // 'tax_rate' => 18,
                // 'alert_quantity' => 8,
                // 'unit' => 'PIECE',
                // 'category' => 'network',
            ],
            [
                'name' => 'Switch Gigabit 8 Ports',
                'sku' => 'SWI-GIG-8P',
                // 'description' => 'Switch Ethernet Gigabit 8 ports',
                'price' => 18000,
                // 'selling_price' => 28000,
                // 'tax_rate' => 18,
                // 'alert_quantity' => 12,
                // 'unit' => 'PIECE',
                // 'category' => 'network',
            ],
        ];

        // Créer les produits pour chaque entreprise
        foreach ($companies as $company) {
            // Chaque entreprise reçoit 70% des produits (sélection aléatoire)
            $companyProducts = collect($productsData)->random(min(22, count($productsData)));

            foreach ($companyProducts as $productData) {
                // // $category = $categories[$productData['category']] ?? null;

                Product::create([
                    'name' => $productData['name'],
                    'sku' => $productData['sku'] . '-' . strtoupper(substr($company->name, 5, 15)),
                    // // 'description' => $productData['description'],
                    'price' => $productData['price'],
                    // // 'selling_price' => $productData['selling_price'],
                    // // 'tax_rate' => $productData['tax_rate'],
                    // // 'alert_quantity' => $productData['alert_quantity'],
                    // // 'unit' => $productData['unit'],
                    // 'is_active' => true,
                    // 'track_stock' => true,
                    'compagnie_id' => $company->id,
                    // // 'category_id' => $category?->id,
                ]);
            }

            $this->command->info("   ✓ Produits créés pour : {$company->name}");
        }
    }
}
