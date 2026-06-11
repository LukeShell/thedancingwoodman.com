<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->foreignId('discount_id')
                ->nullable()
                ->after('shipping_method_name')
                ->constrained()
                ->nullOnDelete();
            $table->string('discount_code')->nullable()->after('discount_id');
            $table->unsignedInteger('discount_total')->default(0)->after('discount_code');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropConstrainedForeignId('discount_id');
            $table->dropColumn(['discount_code', 'discount_total']);
        });
    }
};
