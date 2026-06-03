<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderItemAddon extends Model
{
    protected $fillable = [
        'order_item_id',
        'product_addon_id',
        'name',
        'price',
    ];

    protected $casts = [
        'price' => 'int',
    ];

    public function item(): BelongsTo
    {
        return $this->belongsTo(OrderItem::class, 'order_item_id');
    }

    public function addon(): BelongsTo
    {
        return $this->belongsTo(ProductAddon::class, 'product_addon_id');
    }
}
