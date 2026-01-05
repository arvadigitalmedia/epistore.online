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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->uuid('order_number')->unique(); // e.g., ORD-20260103-0001
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // Buyer
            $table->foreignId('distributor_id')->constrained()->onDelete('cascade'); // Seller
            
            $table->decimal('total_amount', 15, 2);
            $table->string('status')->default('pending'); // pending, processing, shipping, delivered, completed, cancelled
            
            // Shipping Info
            $table->string('shipping_courier')->nullable();
            $table->string('shipping_service')->nullable();
            $table->string('shipping_tracking_number')->nullable();
            $table->decimal('shipping_cost', 15, 2)->default(0);
            $table->text('shipping_address');
            
            // Dates
            $table->timestamp('paid_at')->nullable();
            $table->timestamp('shipped_at')->nullable();
            $table->date('estimated_delivery_date')->nullable();
            $table->timestamp('delivered_at')->nullable(); // System track
            $table->timestamp('received_at')->nullable(); // Customer confirmation
            
            $table->text('notes')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
