<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class StariTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        \App\Models\Stari::factory()->create([
            'id' => 1,
            'nume_stare' => 'Generat',
        ]);

        \App\Models\Stari::factory()->create([
            'id' => 2,
            'nume_stare' => 'Mail Trimis',
        ]);

        \App\Models\Stari::factory()->create([
            'id' => 3,
            'nume_stare' => 'Validat',
        ]);

        \App\Models\Stari::factory()->create([
            'id' => 4,
            'nume_stare' => 'Anulat',
        ]);
    }
}
