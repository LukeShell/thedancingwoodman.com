<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('shipping_zones', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('country_code', 2);
            $table->json('postcode_patterns')->nullable();
            $table->unsignedSmallInteger('priority')->default(100);
            $table->boolean('is_active')->default(true);
            $table->string('method_type', 16);
            $table->unsignedBigInteger('flat_rate')->nullable();
            $table->unsignedBigInteger('free_min_subtotal')->nullable();
            $table->timestamps();

            $table->index(['country_code', 'is_active', 'priority']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('shipping_zones');
    }
};
