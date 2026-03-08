<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'email' => 'admin@amgglobal.fr',
            'password' => 'password',
            'name' => 'Jean',
            'last_name' => 'Kouassi',
            'compagnie_id' => 1,
        ]);
        User::create([
            'email' => 'drizain2.0@gmail.com',
            'password' => 'password',
            'name' => 'Jean',
            'last_name' => 'Kouassi',
            'compagnie_id' => 2,
        ]);
        User::create([
            'email' => 'drissa@gmail.com',
            'password' => 'password',
            'name' => 'Jean',
            'last_name' => 'Kouassi',
            'compagnie_id' => 3,
        ]);
        User::create([
            'email' => 'test@gmail.com',
            'password' => 'password',
            'name' => 'Jean',
            'last_name' => 'Kouassi',
            'compagnie_id' => 4,
        ]);
    }
}
