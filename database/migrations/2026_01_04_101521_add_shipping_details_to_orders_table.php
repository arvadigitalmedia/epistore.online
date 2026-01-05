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
        Schema::table('orders', function (Blueprint $table) {
            // Check if recipient_name exists, if not add it
            if (!Schema::hasColumn('orders', 'recipient_name')) {
                $table->string('recipient_name')->nullable()->after('user_id');
            }
            
            // Add other columns
            if (!Schema::hasColumn('orders', 'recipient_phone')) {
                $table->string('recipient_phone')->nullable()->after('status'); // Place it safely
            }
            
            if (!Schema::hasColumn('orders', 'shipping_note')) {
                $table->text('shipping_note')->nullable()->after('shipping_tracking_number');
            }
            
            if (!Schema::hasColumn('orders', 'coupon_code')) {
                $table->string('coupon_code')->nullable()->after('total_amount');
            }
            
            if (!Schema::hasColumn('orders', 'discount_amount')) {
                    $table->decimal('discount_amount', 12, 2)->default(0)->after('coupon_code');
                }
                if (!Schema::hasColumn('orders', 'payment_method')) {
                    $table->string('payment_method')->nullable()->after('shipping_cost');
                }
                if (!Schema::hasColumn('orders', 'payment_status')) {
                    $table->string('payment_status')->default('pending')->after('payment_method');
                }
            });
        }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['recipient_name', 'recipient_phone', 'shipping_note', 'coupon_code', 'discount_amount']);
        });
    }
};
