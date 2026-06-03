<?php

use App\Models\Category;
use App\Models\Product;
use App\Models\ProductAttribute;
use App\Models\ProductAttributeValue;
use App\Models\ProductVariant;
use Database\Seeders\CatalogSeeder;

it('pins a variant to one value per attribute', function () {
    $product = Product::factory()->create();

    $diameter = ProductAttribute::factory()
        ->for($product)
        ->create(['name' => 'Diameter']);
    $finish = ProductAttribute::factory()
        ->for($product)
        ->create(['name' => 'Finish']);

    $oneTwenty = ProductAttributeValue::factory()
        ->for($diameter, 'attribute')
        ->create(['value' => '120cm']);
    $oak = ProductAttributeValue::factory()
        ->for($finish, 'attribute')
        ->create(['value' => 'Oak']);

    $variant = ProductVariant::factory()
        ->for($product)
        ->create(['price' => 695.00, 'stock_quantity' => 4]);

    $variant->attributeValues()->attach([$oneTwenty->id, $oak->id]);

    $fresh = ProductVariant::with('attributeValues.attribute')->find($variant->id);

    expect($fresh->price)->toEqual('695.00')
        ->and($fresh->stock_quantity)->toBe(4)
        ->and($fresh->attributeValues)->toHaveCount(2)
        ->and($fresh->attributeValues->pluck('value')->all())->toEqualCanonicalizing(['120cm', 'Oak'])
        ->and($fresh->attributeValues->pluck('attribute.name')->all())->toEqualCanonicalizing(['Diameter', 'Finish']);
});

it('attaches a product to multiple nested categories', function () {
    $tables = Category::factory()->create(['name' => 'Tables']);
    $dining = Category::factory()->create(['name' => 'Dining', 'parent_id' => $tables->id]);

    $product = Product::factory()->create();
    $product->categories()->sync([$tables->id, $dining->id]);

    expect($product->fresh()->categories)->toHaveCount(2)
        ->and($dining->fresh()->parent->id)->toBe($tables->id)
        ->and($tables->fresh()->children->pluck('id')->all())->toBe([$dining->id]);
});

it('cascades variant deletes through the attribute-value pivot', function () {
    $product = Product::factory()->create();
    $attr = ProductAttribute::factory()->for($product)->create();
    $value = ProductAttributeValue::factory()->for($attr, 'attribute')->create();
    $variant = ProductVariant::factory()->for($product)->create();
    $variant->attributeValues()->attach($value->id);

    $variant->delete();

    expect(DB::table('product_attribute_value_product_variant')->count())->toBe(0);
});

it('seeds the round dining table with 6 variants and pinned attribute values', function () {
    $this->seed(CatalogSeeder::class);

    $product = Product::with('variants.attributeValues.attribute', 'addons', 'categories')
        ->where('slug', 'rustic-round-oak-dining-table')
        ->first();

    expect($product)->not->toBeNull()
        ->and($product->variants)->toHaveCount(6)
        ->and($product->categories->pluck('slug')->all())->toEqualCanonicalizing(['tables', 'dining-tables']);

    $product->variants->each(function ($v) {
        expect($v->attributeValues)->toHaveCount(2)
            ->and($v->attributeValues->pluck('attribute.name')->all())->toEqualCanonicalizing(['Diameter', 'Finish']);
    });
});

it('seeds the coffee table with a matching bench addon', function () {
    $this->seed(CatalogSeeder::class);

    $product = Product::with('addons')->where('slug', 'chunky-coffee-table')->first();

    expect($product->addons->pluck('name')->all())->toContain('Matching Bench')
        ->and($product->addons->firstWhere('name', 'Matching Bench')->price)->toEqual('175.00');
});
