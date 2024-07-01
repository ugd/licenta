<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class UsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        \App\Models\User::factory()->create([
            'name' => 'Administrator',
            'email' => 'licenta@exemplu.ro',
            'password' => bcrypt('ParolaLICENTA!2@24_'),
            'permisiune_id' => 1,
        ]);
    }
}
