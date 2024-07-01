<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class PermisiuniSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        \App\Models\Permisiuni::factory()->create([
            'nume_permisiune' => 'Admin',
        ]);

        \App\Models\Permisiuni::factory()->create([
            'nume_permisiune' => 'Acces intrari',
        ]);
    }
}
