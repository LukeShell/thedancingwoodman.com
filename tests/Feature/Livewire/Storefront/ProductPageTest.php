<?php

use App\Livewire\Storefront\ProductPage;
use App\Models\Finish;
use App\Models\Product;
use App\Models\TrustBadge;
use App\Support\BasketResolver;

use function Pest\Livewire\livewire;

beforeEach(function () {
    $this->product = Product::factory()->create([
        'name' => 'WILLIAM Block',
        'slug' => 'william-block',
        'is_active' => true,
    ]);

    $size = $this->product->attributes()->create(['name' => 'Size', 'sort_order' => 1]);
    $this->small = $size->values()->create(['value' => 'Small', 'sort_order' => 0]);
    $this->large = $size->values()->create(['value' => 'Large', 'sort_order' => 1]);

    $this->smallVariant = $this->product->variants()->create([
        'sku' => 'W-S', 'price' => 425.00, 'stock_quantity' => 5, 'is_active' => true,
    ]);
    $this->smallVariant->attributeValues()->attach($this->small->id);

    $this->largeVariant = $this->product->variants()->create([
        'sku' => 'W-L', 'price' => 875.00, 'stock_quantity' => 5, 'is_active' => true,
    ]);
    $this->largeVariant->attributeValues()->attach($this->large->id);

    $this->honey = Finish::create(['name' => 'Honey', 'slug' => 'honey', 'hex_color' => '#D4A76A']);
    $this->natural = Finish::create(['name' => 'Natural', 'slug' => 'natural', 'hex_color' => '#E8DCC4']);
    $this->product->finishes()->sync([$this->honey->id, $this->natural->id]);

    $this->product->trustBadges()->sync(
        TrustBadge::create(['icon' => 'truck', 'title' => 'Free UK Delivery', 'subtitle' => '4-6 weeks'])->id,
    );
});

it('defaults selection to the first attribute value and finish', function () {
    livewire(ProductPage::class, ['product' => $this->product])
        ->assertSet('selectedValues.'.$this->product->attributes->first()->id, $this->small->id)
        ->assertSet('selectedFinishId', $this->honey->id);
});

it('renders the price range from variant prices', function () {
    livewire(ProductPage::class, ['product' => $this->product])
        ->assertSee('£425.00')
        ->assertSee('£875.00');
});

it('resolves variant when size selection changes and updates the CTA price', function () {
    $component = livewire(ProductPage::class, ['product' => $this->product]);

    $attributeId = $this->product->attributes->first()->id;

    $component->call('selectValue', $attributeId, $this->large->id)
        ->assertSee('Add to Cart — £875.00');
});

it('adds the selected variant + finish to the basket', function () {
    livewire(ProductPage::class, ['product' => $this->product])
        ->call('selectFinish', $this->natural->id)
        ->call('addToBasket')
        ->assertDispatched('basket-updated');

    $basket = app(BasketResolver::class)->current();
    $item = $basket->items()->first();

    expect($item)->not->toBeNull()
        ->and($item->product_variant_id)->toBe($this->smallVariant->id)
        ->and($item->finish_id)->toBe($this->natural->id)
        ->and($item->quantity)->toBe(1);
});

it('blocks add-to-basket when finishes are enabled but none selected', function () {
    $component = livewire(ProductPage::class, ['product' => $this->product])
        ->set('selectedFinishId', null)
        ->call('addToBasket');

    expect(app(BasketResolver::class)->current()->items()->count())->toBe(0);
});

it('hides the finish picker when product has no finishes attached', function () {
    $this->product->finishes()->detach();

    livewire(ProductPage::class, ['product' => $this->product->fresh()])
        ->assertDontSee('Wood Finish');
});

it('exposes related products excluding the current product', function () {
    $category = $this->product->categories()->create(['name' => 'Blocks', 'slug' => 'blocks']);
    $this->product->categories()->sync([$category->id]);

    $other = Product::factory()->create(['is_active' => true]);
    $other->categories()->sync([$category->id]);

    livewire(ProductPage::class, ['product' => $this->product->fresh()])
        ->assertSee($other->name);
});
