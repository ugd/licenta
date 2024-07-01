<?php

namespace Database\Seeders;

use App\Models\VendoriBilete;
use Illuminate\Database\Seeder;

class VendoriBileteSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $vendors = [
            ['id' => 1, 'nume_vendor' => 'iabilet.ro'],
            ['id' => 2, 'nume_vendor' => 'LiveTickets'],
            ['id' => 3, 'nume_vendor' => 'Bilete.ro'],
            ['id' => 4, 'nume_vendor' => 'MyTicket'],
            ['id' => 5, 'nume_vendor' => 'Eventbook'],
            ['id' => 6, 'nume_vendor' => 'Eventim'],
        ];

        foreach ($vendors as $vendor) {
            VendoriBilete::create($vendor);
        }
    }
}
