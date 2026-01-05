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
        Schema::table('product_stocks', function (Blueprint $table) {
            $table->foreignId('store_location_id')->nullable()->after('product_id')->constrained()->nullOnDelete();
            
            // Drop old unique constraint (distributor + product) because now we can have multiple entries per product (diff stores)
            $table->dropUnique(['distributor_id', 'product_id']);
            
            // Add new unique constraint including store
            // Note: In MySQL, multiple NULLs are allowed in unique index. 
            // We rely on application logic to ensure only one "NULL store" (Main Warehouse) entry exists per product.
            $table->unique(['distributor_id', 'product_id', 'store_location_id'], 'stock_unique_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('product_stocks', function (Blueprint $table) {
            $table->dropForeign(['store_location_id']);
            $table->dropUnique('stock_unique_idx');
            $table->dropColumn('store_location_id');
            
            $table->unique(['distributor_id', 'product_id']);
        });
    }
};
