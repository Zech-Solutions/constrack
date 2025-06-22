<?php

namespace Database\Seeders;

use App\Models\Tenant;
use App\Models\User;
use Illuminate\Database\Seeder;

class TenantSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = User::create([
            'name' => 'Warren G. Munez',
            'email' => 'admin@gmail.com',
            'role' => 'admin',
            'password' => bcrypt('admin'),
        ]);

        $tenant = Tenant::create([
            'name' => 'YWB ENGINEERING & CONSTRUCTION SERVICES',
            'email' => 'ywbecs@gmail.com',
            'contact' => '(+63)9072786893/ (+63)9274905001',
        ]);

        $tenant->users()->attach($user);
    }
}
