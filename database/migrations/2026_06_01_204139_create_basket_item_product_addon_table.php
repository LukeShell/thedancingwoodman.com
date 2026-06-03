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
        Schema::create('basket_item_product_addon', function (Blueprint $table) {
            $table->foreignId('basket_item_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_addon_id')->constrained()->restrictOnDelete();
            $table->primary(['basket_item_id', 'product_addon_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('basket_item_product_addon');
    }
};
