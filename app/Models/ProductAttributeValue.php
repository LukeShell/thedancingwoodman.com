<?php

namespace App\Models;

use Database\Factories\ProductAttributeValueFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class ProductAttributeValue extends Model
{
    /** @use HasFactory<ProductAttributeValueFactory> */
    use HasFactory;

    protected $fillable = [
        'product_attribute_id',
        'value',
        'sort_order',
    ];

    protected $casts = [
        'sort_order' => 'int',
    ];

    public function attribute(): BelongsTo
    {
        return $this->belongsTo(ProductAttribute::class, 'product_attribute_id');
    }

    public function variants(): BelongsToMany
    {
        return $this->belongsToMany(
            ProductVariant::class,
            'product_attribute_value_product_variant',
        );
    }
}
