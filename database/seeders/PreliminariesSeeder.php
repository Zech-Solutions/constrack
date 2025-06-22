<?php

namespace Database\Seeders;

use App\Enums\WorkType;
use App\Models\CategoryMaterial;
use App\Models\Work;
use App\Models\WorkCategory;
use Illuminate\Database\Seeder;

class PreliminariesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $general = Work::create([
            'tenant_id' => 1,
            'name' => 'General Requirement',
        ]);

        $site = Work::create([
            'tenant_id' => 1,
            'name' => 'Site Requirements',
        ]);

        $electrical = Work::create([
            'tenant_id' => 1,
            'name' => 'Electrical Works',
            'scope' => WorkType::MAIN_SCOPE,
        ]);

        WorkCategory::create([
            'tenant_id' => 1,
            'work_id' => $general->id,
            'name' => 'Augmentation',
            'quantity' => 1,
            'unit' => 'lot',
            'is_default' => true,
            'amount' => 16000,
        ]);

        WorkCategory::create([
            'tenant_id' => 1,
            'work_id' => $site->id,
            'name' => 'Mobilization',
            'quantity' => 1,
            'unit' => 'lot',
            'is_default' => true,
            'amount' => 10000,
        ]);

        $commonAreaInstallation = WorkCategory::create([
            'tenant_id' => 1,
            'work_id' => $electrical->id,
            'name' => 'COMON AREA LIGHTING INSTALLATION',
            'quantity' => 1,
            'unit' => 'lot',
        ]);

        $quantities = [17, 70, 1, 20, 2, 2, 2, 1, 1];
        $product_id = 1;
        foreach ($quantities as $quantity) {
            CategoryMaterial::create([
                // "tenant_id" => 1,
                'work_category_id' => $commonAreaInstallation->id,
                'product_id' => $product_id++,
                'quantity' => $quantity,
            ]);
        }

    }
}
