<?php

namespace App\Models;

use App\Enums\DiscountType;
use App\Services\DiscountCalculator;
use Database\Factories\DiscountFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

class Discount extends Model
{
    /** @use HasFactory<DiscountFactory> */
    use HasFactory;

    protected $fillable = [
        'code',
        'description',
        'type',
        'value',
        'is_active',
        'stackable',
        'starts_at',
        'ends_at',
        'max_uses',
        'times_used',
        'min_subtotal',
    ];

    protected $casts = [
        'type' => DiscountType::class,
        'value' => 'decimal:2',
        'is_active' => 'bool',
        'stackable' => 'bool',
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
        'max_uses' => 'int',
        'times_used' => 'int',
        'min_subtotal' => 'decimal:2',
    ];

    public function setCodeAttribute(?string $value): void
    {
        $this->attributes['code'] = $value !== null
            ? strtoupper(trim($value))
            : null;
    }

    public function excludedProducts(): BelongsToMany
    {
        return $this->belongsToMany(Product::class);
    }

    public function excludedCategories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class);
    }

    public function baskets(): HasMany
    {
        return $this->hasMany(Basket::class);
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function isCurrentlyValid(?Carbon $at = null): bool
    {
        $at ??= now();

        if (! $this->is_active) {
            return false;
        }

        if ($this->starts_at !== null && $at->lt($this->starts_at)) {
            return false;
        }

        if ($this->ends_at !== null && $at->gt($this->ends_at)) {
            return false;
        }

        if ($this->max_uses !== null && $this->times_used >= $this->max_uses) {
            return false;
        }

        return true;
    }

    public function isUsable(Basket $basket): bool
    {
        $calculator = app(DiscountCalculator::class);

        $eligibleSubtotalPence = $calculator->eligibleSubtotalPence($basket, $this);

        if ($eligibleSubtotalPence === 0) {
            return false;
        }

        if ($this->min_subtotal !== null) {
            $minSubtotalPence = (int) round(((float) $this->min_subtotal) * 100);

            if ($eligibleSubtotalPence < $minSubtotalPence) {
                return false;
            }
        }

        return true;
    }
}
