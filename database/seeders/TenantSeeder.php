<?php

namespace Database\Seeders;

use App\Models\Tenant;
use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class TenantSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissions = [
            'view any quotation',
            'view quotation',
            'create quotation',
            'update quotation',
            'delete quotation',
            'restore quotation',
        ];

        foreach ($permissions as $perm) {
            Permission::firstOrCreate(['name' => $perm, 'guard_name' => 'web']);
        }


        $admin = User::firstOrCreate(
            ['email' => 'ywbecs@gmail.com'],
            [
                'name' => 'Warren G. Munez',
                'password' => bcrypt('admin'),
            ]
        )->assignRole('ADMIN');

        
        $quotation = User::firstOrCreate(
            ['email' => 'ywbecs1@gmail.com'],
            [
                'name' => 'Quotation User',
                'password' => bcrypt('ywbecs1'),
            ]
        )->assignRole('USER')
        ->givePermissionTo([
            'view any quotation',
            'view quotation',
            'update quotation',
        ]);


        Tenant::firstOrCreate([
            'name' => 'YWB ENGINEERING & CONSTRUCTION SERVICES',
            'slug' => 'ywbecs',
            'email' => 'ywbecs@gmail.com',
            'contact' => '(+63)9072786893/ (+63)9274905001',
        ])->users()->syncWithoutDetaching([$admin->id, $quotation->id]);

        
        $user = User::firstOrCreate(
            ['email' => 'admin@gmail.com'],
            [
                'name' => 'Admin',
                'password' => bcrypt('admin'),
            ]
        )->assignRole('ADMIN');

        Tenant::firstOrCreate([
            'name' => 'Zech Solutions',
            'slug' => 'zech',
            'email' => 'admin@gmail.com',
            'contact' => '(+63)9072786893/ (+63)9274905001',
        ])->users()->syncWithoutDetaching([$user->id]);
    }
}
