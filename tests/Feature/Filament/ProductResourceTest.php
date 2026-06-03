<?php

use App\Filament\Resources\Products\Pages\CreateProduct;
use App\Filament\Resources\Products\Pages\EditProduct;
use App\Filament\Resources\Products\Pages\ListProducts;
use App\Filament\Resources\Products\RelationManagers\AttributesRelationManager;
use App\Filament\Resources\Products\RelationManagers\VariantsRelationManager;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductAttribute;
use App\Models\ProductAttributeValue;
use App\Models\ProductVariant;
use App\Models\User;

use function Pest\Laravel\assertDatabaseHas;
use function Pest\Livewire\livewire;

beforeEach(function () {
    $this->actingAs(User::factory()->create());
});

it('lists products', function () {
    $products = Product::factory()->count(3)->create();

    livewire(ListProducts::class)
        ->assertCanSeeTableRecords($products);
});

it('creates a product with categories', function () {
    $category = Category::factory()->create();

    livewire(CreateProduct::class)
        ->fillForm([
            'name' => 'Round Oak Table',
            'slug' => 'round-oak-table',
            'description' => 'A solid oak round table.',
            'base_price' => 595,
            'is_active' => true,
            'sort_order' => 0,
            'categories' => [$category->id],
        ])
        ->call('create')
        ->assertHasNoFormErrors();

    assertDatabaseHas(Product::class, [
        'slug' => 'round-oak-table',
        'base_price' => '595.00',
    ]);

    $product = Product::where('slug', 'round-oak-table')->first();
    expect($product->categories)->toHaveCount(1)
        ->and($product->categories->first()->id)->toBe($category->id);
});

it('edits a product', function () {
    $product = Product::factory()->create(['name' => 'Old name']);
    $category = Category::factory()->create();
    $product->categories()->attach($category);

    livewire(EditProduct::class, ['record' => $product->id])
        ->fillForm(['name' => 'New name'])
        ->call('save')
        ->assertHasNoFormErrors();

    expect($product->fresh()->name)->toBe('New name');
});

it('creates an attribute with values via the relation manager', function () {
    $product = Product::factory()->create();

    livewire(AttributesRelationManager::class, ['ownerRecord' => $product, 'pageClass' => EditProduct::class])
        ->callTableAction('create', data: [
            'name' => 'Diameter',
            'sort_order' => 1,
            'values' => [
                'a' => ['value' => '100cm', 'sort_order' => 0],
                'b' => ['value' => '120cm', 'sort_order' => 1],
            ],
        ])
        ->assertHasNoTableActionErrors();

    $attribute = $product->fresh()->attributes()->first();
    expect($attribute->name)->toBe('Diameter')
        ->and($attribute->values)->toHaveCount(2)
        ->and($attribute->values->pluck('value')->all())->toBe(['100cm', '120cm']);
});

it('creates a variant pinned to one value per attribute', function () {
    $product = Product::factory()->create();

    $diameter = ProductAttribute::factory()->for($product)->create(['name' => 'Diameter']);
    $finish = ProductAttribute::factory()->for($product)->create(['name' => 'Finish']);
    $oneTwenty = ProductAttributeValue::factory()->for($diameter, 'attribute')->create(['value' => '120cm']);
    $oak = ProductAttributeValue::factory()->for($finish, 'attribute')->create(['value' => 'Oak']);

    livewire(VariantsRelationManager::class, ['ownerRecord' => $product, 'pageClass' => EditProduct::class])
        ->callTableAction('create', data: [
            'sku' => 'RND-120-OAK',
            'price' => 695,
            'stock_quantity' => 4,
            'is_active' => true,
            "attr_{$diameter->id}" => $oneTwenty->id,
            "attr_{$finish->id}" => $oak->id,
        ])
        ->assertHasNoTableActionErrors();

    $variant = ProductVariant::where('sku', 'RND-120-OAK')->first();
    expect($variant)->not->toBeNull()
        ->and($variant->price)->toEqual('695.00')
        ->and($variant->attributeValues)->toHaveCount(2)
        ->and($variant->attributeValues->pluck('id')->all())
        ->toEqualCanonicalizing([$oneTwenty->id, $oak->id]);
});
