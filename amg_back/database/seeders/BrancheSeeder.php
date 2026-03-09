<?php

namespace Database\Seeders;

use App\Models\Branche;
use App\Models\Compagnie;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class BrancheSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
   public function run(): void
    {
        $this->command->info('🏪 Création des branches...');

        $branchesParCompagnie = [
            'AMG Global' => [
                ['name' => 'AMG - Dépôt Principal',  'address' => 'Plateau, Abidjan'],
                ['name' => 'AMG - Cocody',            'address' => 'Cocody 2 Plateaux, Abidjan'],
                ['name' => 'AMG - Yopougon',          'address' => 'Yopougon Mamie Faitai, Abidjan'],
            ],
            'Dadoudi & Frères' => [
                ['name' => 'Dadoudi - Siège',         'address' => 'Marcory, Abidjan'],
                ['name' => 'Dadoudi - Abobo',         'address' => 'Abobo, Abidjan'],
            ],
            'TechDistrib CI' => [
                ['name' => 'TechDistrib - Entrepôt',  'address' => 'Zone Industrielle, Yopougon'],
            ],
        ];

        foreach ($branchesParCompagnie as $compagnieName => $branches) {
            $compagnie = Compagnie::where('name', $compagnieName)->first();

            if (!$compagnie) {
                $this->command->warn("   ⚠ Compagnie introuvable : {$compagnieName}");
                continue;
            }

            foreach ($branches as $data) {
                Branche::create([
                    'compagnie_id' => $compagnie->id,
                    'name'         => $data['name'],
                    'address'      => $data['address'],
                ]);
                $this->command->info("   ✓ {$data['name']}");
            }
        }
    }

}
