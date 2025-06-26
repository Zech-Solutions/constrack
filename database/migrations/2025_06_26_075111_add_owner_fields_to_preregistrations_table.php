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
        Schema::table('preregistrations', function (Blueprint $table) {
            $table->string('domain_name')->unique();
            $table->string('owner_firstname');
            $table->string('owner_middlename')->nullable();
            $table->string('owner_lastname');
            $table->string('owner_email');
            $table->text('address');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('preregistrations', function (Blueprint $table) {
            $table->dropColumn([
                'domain_name',
                'owner_firstname',
                'owner_middlename',
                'owner_lastname',
                'owner_email',
                'address',
            ]);
        });
    }
};
