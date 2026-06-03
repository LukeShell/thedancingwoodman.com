<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('reference')->unique();
            $table->foreignId('basket_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('status', 32)->index();
            $table->char('currency', 3);
            $table->unsignedBigInteger('subtotal');
            $table->unsignedBigInteger('shipping_total')->default(0);
            $table->unsignedBigInteger('tax_total')->default(0);
            $table->unsignedBigInteger('grand_total');
            $table->string('email');
            $table->string('first_name', 100);
            $table->string('last_name', 100);
            $table->string('address_line_1');
            $table->string('address_line_2')->nullable();
            $table->string('city', 100);
            $table->char('country', 2);
            $table->string('state', 100)->nullable();
            $table->string('postal_code', 20);
            $table->timestamp('placed_at')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->timestamp('cancelled_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
