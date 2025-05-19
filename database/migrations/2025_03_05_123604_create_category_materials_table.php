<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('category_materials', function (Blueprint $table) {
            $table->id();
            $table->foreignId('work_category_id')->references('id')->on('work_categories');
            $table->foreignId('product_id')->references('id')->on('products');
            $table->decimal('quantity', 12, 2);
            $table->integer('sequence')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('category_materials');
    }
};
