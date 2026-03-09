<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->command->info('');
        $this->command->info('🚀 Démarrage des seeders...');
        $this->command->info('');

        $this->call([
            CompagnieSeeder::class,  // 1. Compagnies (pas de dépendances)
            BrancheSeeder::class,    // 2. Branches (dépend : compagnies)
            UserSeeder::class,       // 3. Users + pivot branche_user (dépend : compagnies, branches)
            ProductSeeder::class,    // 4. Produits (dépend : compagnies)
            StockSeeder::class,      // 5. Stocks + mouvements (dépend : tout le reste)
        ]);

        $this->command->info('');
        $this->command->info('✅ Base de données initialisée avec succès !');
        $this->command->info('');
        $this->command->table(
            ['Compagnie', 'Admin', 'Manager', 'Opérateur', 'Mot de passe'],
            [
                ['AMG Global',      'admin@amg-global.ci',      'manager@amg-global.ci',      'operateur@amg-global.ci',      'password'],
                ['Dadoudi & Frères','admin@dadoudi-freres.ci',   'manager@dadoudi-freres.ci',  'operateur@dadoudi-freres.ci',  'password'],
                ['TechDistrib CI',  'admin@techdistrib-ci.ci',   'manager@techdistrib-ci.ci',  'operateur@techdistrib-ci.ci',  'password'],
            ]
        );
        $this->command->info('');
    }
}
