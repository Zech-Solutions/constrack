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
        Schema::create('job_orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id');
            
            $table->foreignId('client_id')
                ->constrained()
                ->cascadeOnDelete();
            
            $table->foreignId('quotation_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->decimal('sub_total', 12, 2);
            $table->decimal('vat', 12, 2);
            $table->decimal('total', 12, 2);
            $table->date("jo_date");
            $table->date("delivery_date");
            $table->date("finish_date");
            $table->string("code")->unique();
            $table->enum("status", ["DRAFT", "PENDING", "FINISHED"])->default("DRAFT");
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('job_orders');
    }
};
