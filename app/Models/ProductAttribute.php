<?php

namespace App\Models;

use Database\Factories\ProductAttributeFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProductAttribute extends Model
{
    /** @use HasFactory<ProductAttributeFactory> */
    use HasFactory;

    protected $fillable = [
        'product_id',
        'name',
        'sort_order',
    ];

    protected $casts = [
        'sort_order' => 'int',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function values(): HasMany
    {
        return $this->hasMany(ProductAttributeValue::class);
    }
}
