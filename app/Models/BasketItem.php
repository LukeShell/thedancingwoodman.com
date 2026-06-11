<?php

namespace App\Models;

use Database\Factories\BasketItemFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Collection;

class BasketItem extends Model
{
    /** @use HasFactory<BasketItemFactory> */
    use HasFactory;

    protected $fillable = [
        'basket_id',
        'product_variant_id',
        'finish_id',
        'quantity',
    ];

    protected $casts = [
        'quantity' => 'int',
    ];

    public function basket(): BelongsTo
    {
        return $this->belongsTo(Basket::class);
    }

    public function variant(): BelongsTo
    {
        return $this->belongsTo(ProductVariant::class, 'product_variant_id');
    }

    public function finish(): BelongsTo
    {
        return $this->belongsTo(Finish::class);
    }

    public function addons(): BelongsToMany
    {
        return $this->belongsToMany(
            ProductAddon::class,
            'basket_item_product_addon',
        );
    }

    public function unitPrice(): string
    {
        $variantPrice = (float) $this->variant->price;
        $addonsTotal = (float) $this->addons->sum(fn (ProductAddon $addon) => (float) $addon->price);

        return number_format($variantPrice + $addonsTotal, 2, '.', '');
    }

    public function lineTotal(): string
    {
        return number_format((float) $this->unitPrice() * $this->quantity, 2, '.', '');
    }

    public function cardImageUrl(): ?string
    {
        $image = $this->variant->product->primaryImage();

        if (! $image) {
            return null;
        }

        return $image->hasGeneratedConversion('card') ? $image->getUrl('card') : $image->getUrl();
    }

    public function variantSummary(string $separator = ' / '): string
    {
        return $this->variant->attributeValues
            ->map(fn ($value) => $value->value)
            ->implode($separator);
    }

    /**
     * @return Collection<int, string>
     */
    public function variantLines(): Collection
    {
        return $this->variant->attributeValues
            ->map(fn ($value) => $value->attribute->name.': '.$value->value);
    }
}
