<?php

use App\Http\Controllers\Payments\WebhookController;
use App\Livewire\Storefront\BasketPage;
use App\Livewire\Storefront\CheckoutPage;
use App\Livewire\Storefront\OrderConfirmationPage;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    $featured = Product::query()
        ->where('is_active', true)
        ->orderBy('sort_order')
        ->take(3)
        ->get();

    return view('storefront.home', ['featured' => $featured]);
})->name('home');

Route::get('/shop', function () {
    $categories = Category::query()
        ->whereNull('parent_id')
        ->where('is_active', true)
        ->with(['children' => fn ($q) => $q->where('is_active', true)->orderBy('sort_order')])
        ->orderBy('sort_order')
        ->get();

    $activeCategory = null;
    $productsQuery = Product::query()->where('is_active', true);

    if ($slug = request('category')) {
        $activeCategory = Category::query()->where('slug', $slug)->first();

        if ($activeCategory) {
            $productsQuery->whereHas('categories', fn ($q) => $q->where('categories.id', $activeCategory->id));
        }
    }

    $products = $productsQuery->orderBy('sort_order')->orderBy('name')->get();

    return view('storefront.shop', [
        'categories' => $categories,
        'products' => $products,
        'activeCategory' => $activeCategory,
    ]);
})->name('shop.index');

Route::get('/shop/{product:slug}', function (Product $product) {
    abort_unless($product->is_active, 404);

    $product->load([
        'categories',
        'attributes.values',
        'variants.attributeValues.attribute',
        'addons' => fn ($q) => $q->where('is_active', true)->orderBy('sort_order'),
    ]);

    return view('storefront.product', ['product' => $product]);
})->name('shop.show');

Route::livewire('/basket', BasketPage::class)->name('basket.show');
Route::livewire('/checkout', CheckoutPage::class)->name('checkout.show');
Route::livewire('/checkout/{order:reference}/confirm', OrderConfirmationPage::class)->name('checkout.confirm');

Route::post('/webhooks/payments/{gateway}', WebhookController::class)->name('webhooks.payments');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::view('dashboard', 'dashboard')->name('dashboard');
});

require __DIR__.'/settings.php';
