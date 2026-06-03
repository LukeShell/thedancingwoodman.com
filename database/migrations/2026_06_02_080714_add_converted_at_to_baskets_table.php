<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('baskets', function (Blueprint $table) {
            $table->timestamp('converted_at')->nullable()->after('postal_code');
        });
    }

    public function down(): void
    {
        Schema::table('baskets', function (Blueprint $table) {
            $table->dropColumn('converted_at');
        });
    }
};
