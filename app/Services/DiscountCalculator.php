<?php

namespace App\Services;

use App\Enums\DiscountType;
use App\Models\Basket;
use App\Models\BasketItem;
use App\Models\Discount;

class DiscountCalculator
{
    /**
     * Sum (in pence) of line totals for basket items not excluded
     * by the discount's excluded products or excluded categories.
     */
    public function eligibleSubtotalPence(Basket $basket, Discount $discount): int
    {
        $excludedProductIds = $discount->excludedProducts()->pluck('products.id')->all();
        $excludedCategoryIds = $discount->excludedCategories()->pluck('categories.id')->all();

        $items = $basket->items()
            ->with(['variant.product.categories', 'addons'])
            ->get();

        return $items->sum(function (BasketItem $item) use ($excludedProductIds, $excludedCategoryIds): int {
            $product = $item->variant?->product;

            if ($product === null) {
                return 0;
            }

            if (in_array($product->id, $excludedProductIds, true)) {
                return 0;
            }

            if ($excludedCategoryIds !== []) {
                $productCategoryIds = $product->categories->pluck('id')->all();

                if (array_intersect($productCategoryIds, $excludedCategoryIds) !== []) {
                    return 0;
                }
            }

            return (int) round(((float) $item->lineTotal()) * 100);
        });
    }

    /**
     * Discount amount (in pence) the basket should receive from
     * this discount. Clamped to the eligible subtotal.
     */
    public function amountPence(Basket $basket, Discount $discount): int
    {
        $eligible = $this->eligibleSubtotalPence($basket, $discount);

        if ($eligible === 0) {
            return 0;
        }

        $amount = match ($discount->type) {
            DiscountType::Percentage => (int) floor($eligible * ((float) $discount->value) / 100),
            DiscountType::Fixed => (int) round(((float) $discount->value) * 100),
        };

        return min($amount, $eligible);
    }
}
