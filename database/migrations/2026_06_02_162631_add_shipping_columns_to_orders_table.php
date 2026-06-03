<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->foreignId('shipping_zone_id')
                ->nullable()
                ->after('shipping_total')
                ->constrained('shipping_zones')
                ->nullOnDelete();

            $table->string('shipping_method_name')->nullable()->after('shipping_zone_id');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropConstrainedForeignId('shipping_zone_id');
            $table->dropColumn('shipping_method_name');
        });
    }
};
