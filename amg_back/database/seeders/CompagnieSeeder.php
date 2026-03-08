<?php

namespace Database\Seeders;

use App\Models\Compagnie;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CompagnieSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
         $this->command->info('🏢 Création des entreprises...');

        $companies = [
            [
                'name' => 'AMG Global',
                'slug' => 'AMG Global',
                'email' => 'contact@amgglobal.fr',
                'phone' => '+225 07 12 34 56 78',
                // 'address' => '123 Rue Akoupe, Tchad, Abidjan',
                // 'tax_id' => 'CI-AMG-2020-001',
                // 'is_active' => true,
            ],
            [
                'name' => 'DADOUDI et FRERES',
                'slug' => 'DADOUDI et FRERES',
                'email' => 'contact@dadoudi.fr',
                'phone' => '+225 05 98 76 54 32',
                // 'address' => 'Kobakro, Abidjan, Côte d\'Ivoire',
                // 'tax_id' => 'CI-DAD-2019-045',
                // 'is_active' => true,
            ],
            [
                'name' => 'TRAORE Distribution',
                'slug' => 'TRAORE Distribution',
                'email' => 'contact@traoredist.ci',
                'phone' => '+225 01 45 67 89 12',
                // 'address' => 'Zone Industrielle, Yopougon, Abidjan',
                // 'tax_id' => 'CI-TRA-2021-089',
                // 'is_active' => true,
            ],
            [
                'name' => 'Tech Solutions CI',
                'slug' => 'Tech Solutions CI',
                'email' => 'info@techsolutions.ci',
                'phone' => '+225 07 99 88 77 66',
                // 'address' => 'Plateau, Abidjan',
                // 'tax_id' => 'CI-TEC-2022-156',
                // 'is_active' => true,
            ],
        ];

        foreach ($companies as $company) {
            Compagnie::create($company);
            $this->command->info("   ✓ Entreprise créée : {$company['name']}");
        }
    }
}
