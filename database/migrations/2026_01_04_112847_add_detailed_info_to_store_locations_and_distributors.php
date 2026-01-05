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
        // Add detailed address fields to store_locations
        Schema::table('store_locations', function (Blueprint $table) {
            $table->string('rt_rw')->nullable()->after('address');
            $table->string('subdistrict')->nullable()->after('rt_rw'); // Kelurahan
            $table->string('district')->nullable()->after('subdistrict'); // Kecamatan
            // city, province already exist or can be reused. If not, add them.
            // Check existing columns: city, province are there.
            $table->text('notes')->nullable()->after('longitude');
            $table->boolean('is_primary')->default(false)->after('is_active');
        });

        // Add bank info to distributors
        Schema::table('distributors', function (Blueprint $table) {
            $table->text('bank_account_info')->nullable()->after('config'); // JSON or text
            // logo already exists
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('store_locations', function (Blueprint $table) {
            $table->dropColumn(['rt_rw', 'subdistrict', 'district', 'notes', 'is_primary']);
        });

        Schema::table('distributors', function (Blueprint $table) {
            $table->dropColumn('bank_account_info');
        });
    }
};
