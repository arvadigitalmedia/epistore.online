<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->string('delivery_type')->default('shipping')->after('status'); // shipping, pickup
            $table->foreignId('pickup_store_id')->nullable()->after('shipping_note')->constrained('store_locations')->nullOnDelete();
            $table->dateTime('pickup_at')->nullable()->after('pickup_store_id');
            $table->string('pickup_token')->nullable()->after('pickup_at');
            $table->string('qr_code_path')->nullable()->after('pickup_token');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropForeign(['pickup_store_id']);
            $table->dropColumn(['delivery_type', 'pickup_store_id', 'pickup_at', 'pickup_token', 'qr_code_path']);
        });
    }
};
