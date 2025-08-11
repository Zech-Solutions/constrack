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
        Schema::create('quotations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id');
            $table->foreignId('client_id')->references('id')->on('clients');
            $table->foreignId('project_id')->nullable();
            $table->date('quotation_date');
            $table->string('code')->unique();
            $table->integer('term')->default(30);
            $table->decimal('vat_percent')->default(12);
            $table->decimal('profit_percent')->default(30);
            $table->decimal('labor_percent')->default(40);
            $table->decimal('direct_cost', 12, 2)->default(0);
            $table->decimal('vat_cost', 12, 2)->default(0);
            $table->decimal('total_cost', 12, 2)->default(0);
            $table->string('title');
            $table->string('description')->nullable();
            $table->text('remarks')->nullable();
            $table->text('completion')->nullable();
            $table->string('filename')->nullable();
            $table->enum('status', ['DRAFT', 'PENDING', 'APPROVED', 'DECLINED'])->default('DRAFT');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quotations');
    }
};
