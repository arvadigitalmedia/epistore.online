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
        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->onDelete('cascade');
            $table->foreignId('product_id')->constrained()->onDelete('cascade'); // Keep link even if product deleted? Or use set null? Using cascade for now but saving snapshot data.
            
            // Snapshot data
            $table->string('product_name');
            $table->string('product_sku');
            $table->decimal('price', 15, 2); // Unit price at time of order
            $table->integer('quantity');
            $table->decimal('total_price', 15, 2); // quantity * price
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_items');
    }
};
