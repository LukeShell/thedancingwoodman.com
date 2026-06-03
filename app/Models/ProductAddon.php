<?php

namespace App\Models;

use Database\Factories\ProductAddonFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductAddon extends Model
{
    /** @use HasFactory<ProductAddonFactory> */
    use HasFactory;

    protected $fillable = [
        'product_id',
        'name',
        'description',
        'price',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'is_active' => 'bool',
        'sort_order' => 'int',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
