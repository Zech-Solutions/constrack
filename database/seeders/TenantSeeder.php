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
        $user = User::firstOrCreate(
            ['email' => 'admin@gmail.com'],
            [
                'name' => 'Warren G. Munez',
                'password' => bcrypt('admin'),
            ]
        )->assignRole('TENANT_ADMIN');

        Tenant::firstOrCreate([
            'name' => 'YWB ENGINEERING & CONSTRUCTION SERVICES',
            'email' => 'ywbecs@gmail.com',
            'contact' => '(+63)9072786893/ (+63)9274905001',
        ])->users()->syncWithoutDetaching([$user->id]);
    }
}
