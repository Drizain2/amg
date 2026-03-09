<?php

namespace Database\Seeders;

use App\Models\Compagnie;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;


class CompagnieSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
     public function run(): void
    {
        $this->command->info('🏢 Création des compagnies...');

        $compagnies = [
            [
                'name'  => 'AMG Global',
                'email' => 'contact@amgglobal.ci',
                'phone' => '+225 07 12 34 56 78',
            ],
            [
                'name'  => 'Dadoudi & Frères',
                'email' => 'contact@dadoudi.ci',
                'phone' => '+225 05 98 76 54 32',
            ],
            [
                'name'  => 'TechDistrib CI',
                'email' => 'contact@techdistrib.ci',
                'phone' => '+225 01 45 67 89 12',
            ],
        ];

        foreach ($compagnies as $data) {
            Compagnie::create([
                'name'  => $data['name'],
                'slug'  => Str::slug($data['name']),
                'email' => $data['email'],
                'phone' => $data['phone'],
            ]);
            $this->command->info("   ✓ {$data['name']}");
        }
    }
}
