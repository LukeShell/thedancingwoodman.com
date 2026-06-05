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
        Schema::table('basket_items', function (Blueprint $table) {
            $table->foreignId('finish_id')->nullable()->after('product_variant_id')->constrained()->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('basket_items', function (Blueprint $table) {
            $table->dropConstrainedForeignId('finish_id');
        });
    }
};
