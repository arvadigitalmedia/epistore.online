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
        Schema::create('shipping_calculation_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('distributor_id')->constrained()->onDelete('cascade');
            $table->string('origin_city_id'); // RajaOngkir ID
            $table->string('origin_city_name');
            $table->string('destination_city_id'); // RajaOngkir ID
            $table->string('destination_city_name');
            $table->integer('weight'); // in grams
            $table->string('courier');
            $table->string('service')->nullable();
            $table->decimal('cost', 12, 2);
            $table->decimal('margin', 12, 2)->default(0);
            $table->decimal('total_price', 12, 2);
            $table->json('raw_response')->nullable(); // Store full response for debug
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shipping_calculation_logs');
    }
};
