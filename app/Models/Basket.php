<?php

namespace App\Models;

use Database\Factories\BasketFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Basket extends Model
{
    /** @use HasFactory<BasketFactory> */
    use HasFactory;

    protected $fillable = [
        'token',
        'email',
        'first_name',
        'last_name',
        'address_line_1',
        'address_line_2',
        'city',
        'country',
        'state',
        'postal_code',
        'converted_at',
    ];

    protected $casts = [
        'converted_at' => 'datetime',
    ];

    public function items(): HasMany
    {
        return $this->hasMany(BasketItem::class);
    }

    public function order(): HasOne
    {
        return $this->hasOne(Order::class);
    }

    public function isConverted(): bool
    {
        return $this->converted_at !== null;
    }

    public function itemCount(): int
    {
        return (int) $this->items()->sum('quantity');
    }

    public function subtotal(): string
    {
        $total = $this->items()
            ->with(['variant', 'addons'])
            ->get()
            ->sum(fn (BasketItem $item) => (float) $item->lineTotal());

        return number_format($total, 2, '.', '');
    }

    /**
     * Add a variant (with optional addons) to the basket. Lines with an
     * identical variant + addon set are merged by incrementing quantity.
     *
     * @param  array<int>  $addonIds
     */
    public function addItem(ProductVariant $variant, array $addonIds = [], int $quantity = 1, ?int $finishId = null): BasketItem
    {
        $normalizedAddonIds = collect($addonIds)
            ->map(fn ($id) => (int) $id)
            ->unique()
            ->sort()
            ->values()
            ->all();

        $existing = $this->items()
            ->where('product_variant_id', $variant->id)
            ->where('finish_id', $finishId)
            ->with('addons')
            ->get()
            ->first(function (BasketItem $item) use ($normalizedAddonIds) {
                $itemAddonIds = $item->addons->pluck('id')->map(fn ($id) => (int) $id)->sort()->values()->all();

                return $itemAddonIds === $normalizedAddonIds;
            });

        if ($existing) {
            $existing->increment('quantity', max(1, $quantity));

            return $existing->fresh(['variant', 'addons', 'finish']);
        }

        $item = $this->items()->create([
            'product_variant_id' => $variant->id,
            'finish_id' => $finishId,
            'quantity' => max(1, $quantity),
        ]);

        if ($normalizedAddonIds !== []) {
            $item->addons()->sync($normalizedAddonIds);
        }

        return $item->load(['variant', 'addons', 'finish']);
    }
}
