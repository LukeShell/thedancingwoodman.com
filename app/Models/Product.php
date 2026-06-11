<?php

namespace App\Models;

use Database\Factories\ProductFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class Product extends Model implements HasMedia
{
    /** @use HasFactory<ProductFactory> */
    use HasFactory;

    use InteractsWithMedia;

    protected $fillable = [
        'name',
        'slug',
        'short_description',
        'description',
        'base_price',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'base_price' => 'decimal:2',
        'is_active' => 'bool',
        'sort_order' => 'int',
    ];

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('primary')->useDisk('public')->singleFile();
        $this->addMediaCollection('images')->useDisk('public');
    }

    public function registerMediaConversions(?Media $media = null): void
    {
        $this->addMediaConversion('thumb')
            ->width(200)
            ->height(200);

        $this->addMediaConversion('card')
            ->width(600)
            ->height(600);
    }

    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class);
    }

    public function rooms(): BelongsToMany
    {
        return $this->belongsToMany(Room::class);
    }

    public function attributes(): HasMany
    {
        return $this->hasMany(ProductAttribute::class);
    }

    public function variants(): HasMany
    {
        return $this->hasMany(ProductVariant::class);
    }

    public function addons(): HasMany
    {
        return $this->hasMany(ProductAddon::class);
    }

    public function finishes(): BelongsToMany
    {
        return $this->belongsToMany(Finish::class)
            ->withPivot('sort_order')
            ->orderBy('finish_product.sort_order')
            ->orderBy('finishes.sort_order');
    }

    public function trustBadges(): BelongsToMany
    {
        return $this->belongsToMany(TrustBadge::class)
            ->orderBy('trust_badges.sort_order');
    }

    public function discountExclusions(): BelongsToMany
    {
        return $this->belongsToMany(Discount::class);
    }

    public function primaryImage(): ?Media
    {
        return $this->getFirstMedia('primary') ?? $this->getFirstMedia('images');
    }
}
