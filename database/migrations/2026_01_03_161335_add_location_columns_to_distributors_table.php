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
        Schema::table('distributors', function (Blueprint $table) {
            $table->string('province_id')->nullable()->after('address');
            $table->string('province_name')->nullable()->after('province_id');
            $table->string('city_id')->nullable()->after('province_name');
            $table->string('city_name')->nullable()->after('city_id');
            $table->string('district_id')->nullable()->after('city_name');
            $table->string('district_name')->nullable()->after('district_id');
            $table->string('subdistrict_id')->nullable()->after('district_name');
            $table->string('subdistrict_name')->nullable()->after('subdistrict_id');
            $table->string('postal_code')->nullable()->after('subdistrict_name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('distributors', function (Blueprint $table) {
            $table->dropColumn([
                'province_id', 'province_name', 
                'city_id', 'city_name', 
                'district_id', 'district_name', 
                'subdistrict_id', 'subdistrict_name', 
                'postal_code'
            ]);
        });
    }
};
