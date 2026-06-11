<?php

namespace App\Models;

use App\Enums\OrderStatus;
use Database\Factories\OrderFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    /** @use HasFactory<OrderFactory> */
    use HasFactory;

    protected $fillable = [
        'reference',
        'basket_id',
        'user_id',
        'status',
        'currency',
        'subtotal',
        'shipping_total',
        'shipping_zone_id',
        'shipping_method_name',
        'discount_id',
        'discount_code',
        'discount_total',
        'tax_total',
        'grand_total',
        'email',
        'first_name',
        'last_name',
        'address_line_1',
        'address_line_2',
        'city',
        'country',
        'state',
        'postal_code',
        'placed_at',
        'paid_at',
        'cancelled_at',
        'internal_notes',
    ];

    protected $casts = [
        'status' => OrderStatus::class,
        'subtotal' => 'int',
        'shipping_total' => 'int',
        'discount_total' => 'int',
        'tax_total' => 'int',
        'grand_total' => 'int',
        'placed_at' => 'datetime',
        'paid_at' => 'datetime',
        'cancelled_at' => 'datetime',
    ];

    public function basket(): BelongsTo
    {
        return $this->belongsTo(Basket::class);
    }

    public function discount(): BelongsTo
    {
        return $this->belongsTo(Discount::class);
    }

    public function shippingZone(): BelongsTo
    {
        return $this->belongsTo(ShippingZone::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function getRouteKeyName(): string
    {
        return 'reference';
    }

    public function formattedTotal(): string
    {
        return '&pound;'.number_format($this->grand_total / 100, 2);
    }

    public function isPaid(): bool
    {
        return $this->status === OrderStatus::Paid;
    }

    public function isAwaitingPayment(): bool
    {
        return in_array($this->status, [OrderStatus::Pending, OrderStatus::AwaitingPayment], true);
    }
}
