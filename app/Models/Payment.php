<?php

namespace App\Models;

use App\Enums\PaymentStatus;
use Database\Factories\PaymentFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    /** @use HasFactory<PaymentFactory> */
    use HasFactory;

    protected $fillable = [
        'order_id',
        'gateway',
        'gateway_payment_id',
        'gateway_customer_id',
        'status',
        'amount',
        'currency',
        'payment_method_type',
        'client_secret',
        'metadata',
        'last_error',
        'processed_at',
    ];

    protected $casts = [
        'status' => PaymentStatus::class,
        'amount' => 'int',
        'metadata' => 'array',
        'processed_at' => 'datetime',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }
}
