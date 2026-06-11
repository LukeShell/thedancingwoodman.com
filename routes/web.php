<?php

use App\Http\Controllers\Payments\WebhookController;
use App\Livewire\Storefront\BasketPage;
use App\Livewire\Storefront\CheckoutPage;
use App\Livewire\Storefront\OrderConfirmationPage;
use App\Livewire\Storefront\ProductPage;
use App\Models\Category;
use App\Models\Product;
use App\Models\Room;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    $featured = Product::query()
        ->where('is_active', true)
        ->orderBy('sort_order')
        ->take(3)
        ->get();

    $categories = [
        [
            'label' => __('Dining Tables'),
            'meta' => __('Large Rectangular | Round | Indoor & Outdoor'),
            'span' => 'md:col-span-8',
            'image' => asset('images/home-bento-table.jpg'),
        ],
        [
            'label' => __('Coffee Tables'),
            'meta' => __('Wooden Coffee | Side Tables'),
            'span' => 'md:col-span-4',
            'image' => asset('images/home-bento-coffee.jpg'),
        ],
        [
            'label' => __('Doors'),
            'meta' => __('Industrial Steel Runner | Farmhouse'),
            'span' => 'md:col-span-5',
            'image' => asset('images/home-bento-door.jpg'),
        ],
        [
            'label' => __('Accessories'),
            'meta' => __('Bath Caddies | Serving Trays | Boards'),
            'span' => 'md:col-span-7',
            'image' => asset('images/home-bento-bath.jpg'),
        ],
    ];

    return view('storefront.home', [
        'featured' => $featured,
        'categories' => $categories,
    ]);
})->name('home');

Route::get('/shop', function () {
    $categories = Category::query()
        ->whereNull('parent_id')
        ->where('is_active', true)
        ->withCount(['products' => fn ($q) => $q->where('is_active', true)])
        ->orderBy('sort_order')
        ->get();

    $rooms = Room::query()
        ->where('is_active', true)
        ->withCount(['products' => fn ($q) => $q->where('is_active', true)])
        ->orderBy('sort_order')
        ->get();

    $totalCount = Product::query()->where('is_active', true)->count();

    $activeCategory = null;
    $activeRoom = null;
    $productsQuery = Product::query()->where('is_active', true);

    if ($slug = request('category')) {
        $activeCategory = Category::query()->where('slug', $slug)->first();

        if ($activeCategory) {
            $productsQuery->whereHas('categories', fn ($q) => $q->where('categories.id', $activeCategory->id));
        }
    }

    if ($roomSlug = request('room')) {
        $activeRoom = Room::query()->where('slug', $roomSlug)->first();

        if ($activeRoom) {
            $productsQuery->whereHas('rooms', fn ($q) => $q->where('rooms.id', $activeRoom->id));
        }
    }

    $sort = request('sort', 'featured');

    match ($sort) {
        'price_asc' => $productsQuery->orderBy('base_price'),
        'price_desc' => $productsQuery->orderByDesc('base_price'),
        'newest' => $productsQuery->orderByDesc('created_at'),
        default => $productsQuery->orderBy('sort_order')->orderBy('name'),
    };

    $products = $productsQuery->paginate(12)->withQueryString();

    return view('storefront.shop', [
        'categories' => $categories,
        'rooms' => $rooms,
        'totalCount' => $totalCount,
        'products' => $products,
        'activeCategory' => $activeCategory,
        'activeRoom' => $activeRoom,
        'sort' => $sort,
    ]);
})->name('shop.index');

Route::livewire('/shop/{product:slug}', ProductPage::class)->name('shop.show');

Route::livewire('/basket', BasketPage::class)->name('basket.show');
Route::livewire('/checkout', CheckoutPage::class)->name('checkout.show');
Route::livewire('/checkout/{order:reference}/confirm', OrderConfirmationPage::class)->name('checkout.confirm');

Route::post('/webhooks/payments/{gateway}', WebhookController::class)->name('webhooks.payments');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::view('dashboard', 'dashboard')->name('dashboard');
});

require __DIR__.'/settings.php';
