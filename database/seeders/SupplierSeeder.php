<?php

namespace Database\Seeders;

use App\Enums\SupplierType;
use App\Models\Supplier;
use Illuminate\Database\Seeder;

class SupplierSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Supplier::factory()
            ->count(5)
            ->create();

        Supplier::factory()
            ->count(5)
            ->create([
                'type' => SupplierType::SUBCON,
            ]);
    }
}
