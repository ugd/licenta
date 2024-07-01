<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class InviteTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        \App\Models\InviteType::factory()->create([
            'invite_type' => 'General Access',
        ]);

        \App\Models\InviteType::factory()->create([
            'invite_type' => 'VIP',
        ]);
    }
}
