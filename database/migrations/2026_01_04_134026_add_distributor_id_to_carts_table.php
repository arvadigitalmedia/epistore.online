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
        Schema::table('carts', function (Blueprint $table) {
            $table->foreignId('distributor_id')->nullable()->after('user_id')->constrained()->nullOnDelete();
            // Ensure one cart per user per distributor
            $table->unique(['user_id', 'distributor_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('carts', function (Blueprint $table) {
            $table->dropForeign(['distributor_id']);
            $table->dropUnique(['user_id', 'distributor_id']);
            $table->dropColumn('distributor_id');
        });
    }
};
