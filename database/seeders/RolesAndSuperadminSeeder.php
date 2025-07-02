<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Spatie\Permission\Models\Role;

class RolesAndSuperadminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Role::firstOrCreate(['name' => 'SUPERADMIN', 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'TENANT_ADMIN', 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'USER', 'guard_name' => 'web']);
    }
}
