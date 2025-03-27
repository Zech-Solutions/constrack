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
        Schema::create('clients', function (Blueprint $table) {
            $table->id();

            $table->string('name');
            $table->string('email')->unique()->nullable();
            $table->string('phone')->nullable();
            $table->string('company')->nullable();
            $table->enum('type', ['individual', 'business'])->default('individual');

            $table->text('address')->nullable();
            $table->string('city')->nullable();
            $table->string('state')->nullable();
            $table->string('postal_code')->nullable();
            $table->string('country')->nullable();

            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('supplier_id')->nullable()->constrained()->onDelete('set null');

            $table->decimal('credit_limit', 10, 2)->nullable();
            $table->enum('payment_terms', ['net_15', 'net_30', 'cod'])->default('net_30');

            $table->text('notes')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('clients');
    }
};
