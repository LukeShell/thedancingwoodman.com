<?php

use App\Models\Category;
use App\Models\Product;
use App\Models\Room;
use Illuminate\Foundation\Testing\RefreshDatabase;

use function Pest\Laravel\get;

uses(RefreshDatabase::class);

it('attaches products to rooms via the belongs-to-many relation', function () {
    $room = Room::factory()->create(['name' => 'Lounge', 'slug' => 'lounge']);
    $product = Product::factory()->create();

    $product->rooms()->attach($room);

    expect($product->fresh()->rooms)->toHaveCount(1);
    expect($room->fresh()->products)->toHaveCount(1);
});

it('filters the shop index by room slug', function () {
    $lounge = Room::factory()->create(['name' => 'Lounge', 'slug' => 'lounge', 'is_active' => true]);
    $kitchen = Room::factory()->create(['name' => 'Kitchen', 'slug' => 'kitchen', 'is_active' => true]);

    $sofa = Product::factory()->create(['name' => 'Oak Sofa', 'slug' => 'oak-sofa', 'is_active' => true]);
    $worktop = Product::factory()->create(['name' => 'Pine Worktop', 'slug' => 'pine-worktop', 'is_active' => true]);

    $sofa->rooms()->attach($lounge);
    $worktop->rooms()->attach($kitchen);

    get(route('shop.index', ['room' => 'lounge']))
        ->assertOk()
        ->assertSee('Oak Sofa')
        ->assertDontSee('Pine Worktop');
});

it('intersects category and room filters', function () {
    $tables = Category::factory()->create(['name' => 'Tables', 'slug' => 'tables', 'is_active' => true]);
    $chairs = Category::factory()->create(['name' => 'Chairs', 'slug' => 'chairs', 'is_active' => true]);
    $lounge = Room::factory()->create(['name' => 'Lounge', 'slug' => 'lounge', 'is_active' => true]);

    $loungeTable = Product::factory()->create(['name' => 'Lounge Table', 'slug' => 'lounge-table', 'is_active' => true]);
    $loungeTable->categories()->attach($tables);
    $loungeTable->rooms()->attach($lounge);

    $loungeChair = Product::factory()->create(['name' => 'Lounge Chair', 'slug' => 'lounge-chair', 'is_active' => true]);
    $loungeChair->categories()->attach($chairs);
    $loungeChair->rooms()->attach($lounge);

    get(route('shop.index', ['category' => 'tables', 'room' => 'lounge']))
        ->assertOk()
        ->assertSee('Lounge Table')
        ->assertDontSee('Lounge Chair');
});
