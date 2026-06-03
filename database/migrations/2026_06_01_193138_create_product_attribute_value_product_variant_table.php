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
        Schema::create('product_attribute_value_product_variant', function (Blueprint $table) {
            $table->foreignId('product_variant_id');
            $table->foreignId('product_attribute_value_id');

            $table->primary(
                ['product_variant_id', 'product_attribute_value_id'],
                'pavpv_primary',
            );

            $table->foreign('product_variant_id', 'pavpv_variant_fk')
                ->references('id')->on('product_variants')->cascadeOnDelete();

            $table->foreign('product_attribute_value_id', 'pavpv_value_fk')
                ->references('id')->on('product_attribute_values')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_attribute_value_product_variant');
    }
};
