<?php

namespace App\Models;

use Database\Factories\OrderItemFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class OrderItem extends Model
{
    /** @use HasFactory<OrderItemFactory> */
    use HasFactory;

    protected $fillable = [
        'order_id',
        'product_variant_id',
        'product_name',
        'variant_label',
        'sku',
        'unit_price',
        'quantity',
        'line_total',
    ];

    protected $casts = [
        'unit_price' => 'int',
        'quantity' => 'int',
        'line_total' => 'int',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function variant(): BelongsTo
    {
        return $this->belongsTo(ProductVariant::class, 'product_variant_id');
    }

    public function addons(): HasMany
    {
        return $this->hasMany(OrderItemAddon::class);
    }
}
