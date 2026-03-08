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

        $amg = Compagnie::where('email', 'contact@amgglobal.fr')->first();
        $dadoudi = Compagnie::where('email', 'contact@dadoudi.fr')->first();
        $traore = Compagnie::where('email', 'contact@traoredist.ci')->first();
        $techsolutions = Compagnie::where('email', 'info@techsolutions.ci')->first();

        // Branches AMG Global
        if ($amg) {
            $this->createBranches($amg, [
                [
                    'name' => 'AMG Akoupe',
                    // 'code' => 'AMG-AKO-001',
                    'address' => 'Tchad Akoupe, Abidjan',
                    // 'phone' => '+225 07 11 11 11 11',
                    // 'email' => 'akoupe@amgglobal.fr',
                ],
                [
                    'name' => 'AMG Cocody',
                    // 'code' => 'AMG-COC-002',
                    'address' => 'Cocody 2 Plateaux, Abidjan',
                    // 'phone' => '+225 07 22 22 22 22',
                    // 'email' => 'cocody@amgglobal.fr',
                ],
                [
                    'name' => 'AMG Yopougon',
                    // 'code' => 'AMG-YOP-003',
                    'address' => 'Yopougon Mamie Faitai, Abidjan',
                    // 'phone' => '+225 07 33 33 33 33',
                    // 'email' => 'yopougon@amgglobal.fr',
                ],
            ]);
        }

        // Branches DADOUDI
        if ($dadoudi) {
            $this->createBranches($dadoudi, [
                [
                    'name' => 'DADOUDI Kobakro',
                    // 'code' => 'DAD-KOB-001',
                    'address' => 'Kobakro, Abidjan',
                    // 'phone' => '+225 05 44 44 44 44',
                    // 'email' => 'kobakro@dadoudi.fr',
                ],
                [
                    'name' => 'DADOUDI Adjamé',
                    // 'code' => 'DAD-ADJ-002',
                    'address' => 'Adjamé 220 Logements, Abidjan',
                    // 'phone' => '+225 05 55 55 55 55',
                    // 'email' => 'adjame@dadoudi.fr',
                ],
                [
                    'name' => 'DADOUDI Marcory',
                    // 'code' => 'DAD-MAR-003',
                    'address' => 'Marcory Zone 4, Abidjan',
                    // 'phone' => '+225 05 66 66 66 66',
                    // 'email' => 'marcory@dadoudi.fr',
                ],
            ]);
        }

        // Branches TRAORE Distribution
        if ($traore) {
            $this->createBranches($traore, [
                [
                    'name' => 'TRAORE Abobo',
                    // 'code' => 'TRA-ABO-001',
                    'address' => 'Abobo Gare, Abidjan',
                    // 'phone' => '+225 01 77 77 77 77',
                    // 'email' => 'abobo@traoredist.ci',
                ],
                [
                    'name' => 'TRAORE Port-Bouët',
                    // 'code' => 'TRA-PBO-002',
                    'address' => 'Port-Bouët Vridi, Abidjan',
                    // 'phone' => '+225 01 88 88 88 88',
                    // 'email' => 'portbouet@traoredist.ci',
                ],
            ]);
        }

        // Branches Tech Solutions
        if ($techsolutions) {
            $this->createBranches($techsolutions, [
                [
                    'name' => 'Tech Solutions Plateau',
                    // 'code' => 'TEC-PLA-001',
                    'address' => 'Plateau Centre, Abidjan',
                    // 'phone' => '+225 07 99 99 99 99',
                    // 'email' => 'plateau@techsolutions.ci',
                ],
                [
                    'name' => 'Tech Solutions Marcory',
                    // 'code' => 'TEC-MAR-002',
                    'address' => 'Marcory Remblais, Abidjan',
                    // 'phone' => '+225 07 00 00 00 00',
                    // 'email' => 'marcory@techsolutions.ci',
                ],
            ]);
        }
    }

    private function createBranches(Compagnie $company, array $branches): void
    {
        foreach ($branches as $branchData) {
            $branchData['compagnie_id'] = $company->id;
            // $branchData['is_active'] = true;

            Branche::create($branchData);
            $this->command->info("   ✓ Branche créée : {$branchData['name']}");
        }
    }
}
