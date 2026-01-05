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
        Schema::create('distributor_domains', function (Blueprint $table) {
            $table->id();
            $table->foreignId('distributor_id')->constrained()->cascadeOnDelete();
            $table->string('domain')->unique(); // custom domain (e.g. mystore.com)
            $table->string('status')->default('pending'); // pending, verified, active, invalid
            $table->text('dns_verification_record')->nullable(); // TXT record to verify ownership
            $table->boolean('is_primary')->default(false); // If true, this is the main custom domain
            $table->timestamp('verified_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('distributor_domains');
    }
};
