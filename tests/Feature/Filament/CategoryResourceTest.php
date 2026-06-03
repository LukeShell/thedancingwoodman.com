<?php

use App\Filament\Resources\Categories\Pages\CreateCategory;
use App\Filament\Resources\Categories\Pages\EditCategory;
use App\Filament\Resources\Categories\Pages\ListCategories;
use App\Models\Category;
use App\Models\User;

use function Pest\Laravel\assertDatabaseHas;
use function Pest\Livewire\livewire;

beforeEach(function () {
    $this->actingAs(User::factory()->create());
});

it('lists categories', function () {
    $categories = Category::factory()->count(3)->create();

    livewire(ListCategories::class)
        ->assertCanSeeTableRecords($categories);
});

it('creates a category', function () {
    livewire(CreateCategory::class)
        ->fillForm([
            'name' => 'Tables',
            'slug' => 'tables',
            'sort_order' => 1,
            'is_active' => true,
        ])
        ->call('create')
        ->assertHasNoFormErrors();

    assertDatabaseHas(Category::class, [
        'name' => 'Tables',
        'slug' => 'tables',
    ]);
});

it('edits a category with a parent', function () {
    $parent = Category::factory()->create(['name' => 'Tables']);
    $child = Category::factory()->create(['name' => 'Coffee Tables']);

    livewire(EditCategory::class, ['record' => $child->id])
        ->fillForm(['parent_id' => $parent->id])
        ->call('save')
        ->assertHasNoFormErrors();

    expect($child->fresh()->parent_id)->toBe($parent->id);
});

it('validates required fields', function () {
    livewire(CreateCategory::class)
        ->fillForm([
            'name' => null,
            'slug' => null,
        ])
        ->call('create')
        ->assertHasFormErrors([
            'name' => 'required',
            'slug' => 'required',
        ]);
});
