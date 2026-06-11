<?php

namespace App\Models;

use Database\Factories\FinishFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class Finish extends Model implements HasMedia
{
    /** @use HasFactory<FinishFactory> */
    use HasFactory;

    use InteractsWithMedia;

    protected $fillable = [
        'name',
        'slug',
        'hex_color',
        'description',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'sort_order' => 'int',
        'is_active' => 'bool',
    ];

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('swatch')->useDisk('public')->singleFile();
    }

    public function registerMediaConversions(?Media $media = null): void
    {
        $this->addMediaConversion('thumb')
            ->width(96)
            ->height(96);
    }

    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class)->withPivot('sort_order');
    }

    public function swatchUrl(): ?string
    {
        return $this->getFirstMediaUrl('swatch', 'thumb') ?: null;
    }
}
