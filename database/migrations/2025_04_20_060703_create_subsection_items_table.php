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
        Schema::create('subsection_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sub_section_id')->references('id')->on('sub_sections');
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
        Schema::dropIfExists('subsection_items');
    }
};
