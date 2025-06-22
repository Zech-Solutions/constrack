<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\ProductCategory;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $lighting = ProductCategory::create(['name' => 'Lighting', 'tenant_id' => 1]);
        $conduits = ProductCategory::create(['name' => 'Conduits', 'tenant_id' => 1]);
        $accessories = ProductCategory::create(['name' => 'Electrical Accessories', 'tenant_id' => 1]);
        $wires = ProductCategory::create(['name' => 'Wires & Cables', 'tenant_id' => 1]);
        $support = ProductCategory::create(['name' => 'Mounting & Support', 'tenant_id' => 1]);
        $misc = ProductCategory::create(['name' => 'Miscellaneous/Consumables', 'tenant_id' => 1]);

        Product::create([
            'tenant_id' => 1,
            'product_category_id' => $lighting->id,
            'name' => 'Lighting Fixtures',
            'unit' => 'pcs',
            'code' => 'LF001',
            'description' => null,
            'quantity' => 0.00,
            'price' => 0.00,
        ]);

        Product::create([
            'tenant_id' => 1,
            'product_category_id' => $conduits->id,
            'name' => 'IMC Conduits 25mm',
            'unit' => 'm',
            'code' => 'IC001',
            'description' => null,
            'quantity' => 0.00,
            'price' => 0.00,
        ]);

        Product::create([
            'tenant_id' => 1,
            'product_category_id' => $accessories->id,
            'name' => 'Fittings',
            'unit' => 'set',
            'code' => 'FT001',
            'description' => null,
            'quantity' => 0.00,
            'price' => 0.00,
        ]);

        Product::create([
            'tenant_id' => 1,
            'product_category_id' => $accessories->id,
            'name' => 'Junction box',
            'unit' => 'pcs',
            'code' => 'JB001',
            'description' => null,
            'quantity' => 0.00,
            'price' => 0.00,
        ]);

        Product::create([
            'tenant_id' => 1,
            'product_category_id' => $wires->id,
            'name' => 'Wires (TTHN) Red 3.5mm²',
            'unit' => 'm',
            'code' => 'WR001',
            'description' => null,
            'quantity' => 0.00,
            'price' => 0.00,
        ]);

        Product::create([
            'tenant_id' => 1,
            'product_category_id' => $wires->id,
            'name' => 'Wires (TTHN) White 3.5mm²',
            'unit' => 'm',
            'code' => 'WW001',
            'description' => null,
            'quantity' => 0.00,
            'price' => 0.00,
        ]);

        Product::create([
            'tenant_id' => 1,
            'product_category_id' => $wires->id,
            'name' => 'Wires (TTHN) Green 3.5mm²',
            'unit' => 'm',
            'code' => 'WG001',
            'description' => null,
            'quantity' => 0.00,
            'price' => 0.00,
        ]);

        Product::create([
            'tenant_id' => 1,
            'product_category_id' => $support->id,
            'name' => 'Hanger/Support',
            'unit' => 'pcs',
            'code' => 'HS001',
            'description' => null,
            'quantity' => 0.00,
            'price' => 0.00,
        ]);

        Product::create([
            'tenant_id' => 1,
            'product_category_id' => $misc->id,
            'name' => 'Consumables',
            'unit' => 'set',
            'code' => 'CS001',
            'description' => null,
            'quantity' => 0.00,
            'price' => 0.00,
        ]);
    }
}
