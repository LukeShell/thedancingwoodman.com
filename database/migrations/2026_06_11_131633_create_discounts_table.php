<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('discounts', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->string('description')->nullable();
            $table->string('type');
            $table->decimal('value', 10, 2);
            $table->boolean('is_active')->default(true);
            $table->boolean('stackable')->default(false);
            $table->timestamp('starts_at')->nullable();
            $table->timestamp('ends_at')->nullable();
            $table->unsignedInteger('max_uses')->nullable();
            $table->unsignedInteger('times_used')->default(0);
            $table->decimal('min_subtotal', 10, 2)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('discounts');
    }
};
