<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->string('gateway', 32)->index();
            $table->string('gateway_payment_id')->nullable();
            $table->string('gateway_customer_id')->nullable();
            $table->string('status', 32)->index();
            $table->unsignedBigInteger('amount');
            $table->char('currency', 3);
            $table->string('payment_method_type')->nullable();
            $table->text('client_secret')->nullable();
            $table->json('metadata')->nullable();
            $table->text('last_error')->nullable();
            $table->timestamp('processed_at')->nullable();
            $table->timestamps();

            $table->unique(['gateway', 'gateway_payment_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
