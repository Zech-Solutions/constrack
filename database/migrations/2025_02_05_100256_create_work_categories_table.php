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
        Schema::create('work_categories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id');
            $table->foreignId('work_id')->references('id')->on('works');
            $table->string('name');
            $table->string('description')->nullable();
            $table->string('unit')->default('lot');
            $table->decimal('quantity', 12, 2)->default(1);
            $table->decimal('amount', 12, 2)->default(0);
            $table->boolean('is_default')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('work_categories');
    }
};
