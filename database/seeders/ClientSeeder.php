<?php

namespace Database\Seeders;

use App\Models\Client;
use Illuminate\Database\Seeder;

class ClientSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Client::create([
            'tenant_id' => 1,
            'name' => 'Ayala Malls Capitol Central',
            'email' => 'ayala@gmail.com',
            'phone' => '11111',
            'company' => 'Ayala',
            'tin' => '-----',
            'type' => 'corporate',
            'address' => 'Brgy. Alijis',
            'city' => 'Bacolod City',
            'state' => 'Negros Occidental',
            'postal_code' => '6100',
            'country' => 'Philippines',
            'credit_limit' => 10000.00,
            'payment_term' => 30,
            'is_active' => 1,
        ]);
    }
}
