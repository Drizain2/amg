<?php

namespace Database\Seeders;

use App\Models\Branche;
use App\Models\Compagnie;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;


class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('👤 Création des utilisateurs...');

        $compagnies = Compagnie::all();

        foreach ($compagnies as $compagnie) {
            // Bypass du CompagnieScope — on est hors auth dans le seeder
            $branches = Branche::withoutGlobalScopes()
                ->where('compagnie_id', $compagnie->id)
                ->get();

            if ($branches->isEmpty()) {
                $this->command->warn("   ⚠ Pas de branches pour : {$compagnie->name}");
                continue;
            }

            $slug = Str::slug($compagnie->name);

            // ── Admin ────────────────────────────────────────────────────
            $admin = User::create([
                'name' => 'Admin',
                'last_name' => $compagnie->name,
                'email' => "admin@{$slug}.ci",
                'password' => 'password',
                'role' => 'admin',
                'compagnie_id' => $compagnie->id,
            ]);
            $admin->branches()->attach($branches->first()->id);

            // ── Manager ──────────────────────────────────────────────────
            // Assigné aux 2 premières branches (ou 1 si une seule existe)
            $manager = User::create([
                'name' => 'Manager',
                'last_name' => $compagnie->name,
                'email' => "manager@{$slug}.ci",
                'password' => 'password',
                'role' => 'manager',
                'compagnie_id' => $compagnie->id,
            ]);
            $manager->branches()->attach(
                $branches->take(2)->pluck('id')->toArray()
            );

            // ── Opérateur ────────────────────────────────────────────────
            // Assigné uniquement à la première branche
            $operator = User::create([
                'name' => 'Opérateur',
                'last_name' => $compagnie->name,
                'email' => "operateur@{$slug}.ci",
                'password' => 'password',
                'role' => 'operator',
                'compagnie_id' => $compagnie->id,
            ]);
            $operator->branches()->attach($branches->first()->id);

            $this->command->info("   ✓ {$compagnie->name} → 3 users créés");
            $this->command->line("       admin@{$slug}.ci | manager@{$slug}.ci | operateur@{$slug}.ci  (mdp: password)");
        }
    }
}
