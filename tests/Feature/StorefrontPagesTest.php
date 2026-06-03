<?php

use App\Models\Product;
use Database\Seeders\CatalogSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

use function Pest\Laravel\get;

uses(RefreshDatabase::class);

beforeEach(fn () => $this->seed(CatalogSeeder::class));

it('renders the homepage with featured products', function () {
    get(route('home'))
        ->assertOk()
        ->assertSee('The Dancing Woodman')
        ->assertSee('Rustic Round Oak Dining Table');
});

it('renders the shop index and lists products', function () {
    get(route('shop.index'))
        ->assertOk()
        ->assertSee('Shop')
        ->assertSee('Rustic Round Oak Dining Table')
        ->assertSee('Reclaimed Wood TV Unit')
        ->assertSee('Chunky Coffee Table');
});

it('filters shop index by category', function () {
    get(route('shop.index', ['category' => 'living-room']))
        ->assertOk()
        ->assertSee('Showing')
        ->assertSee('Reclaimed Wood TV Unit')
        ->assertDontSee('Rustic Round Oak Dining Table');
});

it('renders a product page with variants and addons', function () {
    get(route('shop.show', ['product' => 'chunky-coffee-table']))
        ->assertOk()
        ->assertSee('Chunky Coffee Table')
        ->assertSee('Finish')
        ->assertSee('Matching Bench')
        ->assertSee('Available variants');
});

it('returns 404 for an inactive product', function () {
    Product::query()->where('slug', 'chunky-coffee-table')->update(['is_active' => false]);

    get(route('shop.show', ['product' => 'chunky-coffee-table']))
        ->assertNotFound();
});
