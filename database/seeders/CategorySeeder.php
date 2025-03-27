<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Category::create([
            "name"=> "Electronics",
            "slug"=> "electronics",
            "description"=> "Electronics",
            "is_active"=> true,
        ]);
        Category::create([
            "name"=> "Books",
            "slug"=> "books",
            "description"=> "Books",
            "is_active"=> true,
        ]);
        Category::create([
            "name"=> "Clothes",
            "slug"=> "clothes",
            "description"=> "Clothes",
            "is_active"=> true,
        ]);
        Category::create([
            "name"=> "Furniture",
            "slug"=> "furniture",
            "description"=> "Furniture",
            "is_active"=> true,
        ]);
        Category::create([
            "name"=> "Toys",
            "slug"=> "toys",
            "description"=> "Toys",
            "is_active"=> true,
        ]);
    }
}
