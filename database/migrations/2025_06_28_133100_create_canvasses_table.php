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
        Schema::create('canvasses', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->date('canvass_date');
            $table->foreignId('tenant_id');
            $table->foreignId('requisition_id')->constrained()->cascadeOnDelete();
            $table->foreignId('canvassed_by')->constrained('users')->cascadeOnDelete();
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('rejected')->nullable()->constrained('users')->nullOnDelete();
            $table->enum('status', ['DRAFT', 'SUBMITTED', 'APPROVED', 'REJECTED'])->default('DRAFT');
            $table->text('remarks')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('canvasses');
    }
};
