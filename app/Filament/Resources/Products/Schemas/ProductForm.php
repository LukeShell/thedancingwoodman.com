<?php

namespace App\Filament\Resources\Products\Schemas;

use App\Models\TrustBadge;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class ProductForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Details')
                    ->columns(2)
                    ->columnSpan(2)
                    ->schema([
                        TextInput::make('name')
                            ->required()
                            ->live(onBlur: true)
                            ->afterStateUpdated(fn (Set $set, ?string $state) => $set(
                                'slug',
                                Str::slug($state ?? ''),
                            )),

                        TextInput::make('slug')
                            ->required()
                            ->unique(ignoreRecord: true),

                        RichEditor::make('description')
                            ->required()
                            ->columnSpanFull(),
                    ]),

                Section::make('Pricing & status')
                    ->columns(1)
                    ->schema([
                        TextInput::make('base_price')
                            ->label('Base "from" price')
                            ->helperText('Display price. Actual price comes from the variants.')
                            ->required()
                            ->numeric()
                            ->prefix('£')
                            ->minValue(0),

                        TextInput::make('sort_order')
                            ->required()
                            ->numeric()
                            ->default(0),

                        Toggle::make('is_active')
                            ->default(true)
                            ->inline(false),
                    ]),

                Section::make('Images')
                    ->columns(2)
                    ->columnSpan(2)
                    ->schema([
                        SpatieMediaLibraryFileUpload::make('primary_image')
                            ->label('Primary image')
                            ->collection('primary')
                            ->image()
                            ->imageEditor(),

                        SpatieMediaLibraryFileUpload::make('gallery_images')
                            ->label('Gallery')
                            ->collection('images')
                            ->multiple()
                            ->image()
                            ->reorderable()
                            ->appendFiles()
                            ->imageEditor(),
                    ]),

                Section::make('Categories')
                    ->schema([
                        Select::make('categories')
                            ->relationship('categories', 'name')
                            ->multiple()
                            ->preload()
                            ->searchable()
                            ->required(),
                    ]),

                Section::make('Customisation options')
                    ->description('Choose which finishes and trust badges appear on this product. Leaving finishes empty hides the finish picker on the storefront.')
                    ->columnSpan(2)
                    ->schema([
                        Select::make('finishes')
                            ->relationship('finishes', 'name')
                            ->multiple()
                            ->preload()
                            ->helperText('Empty = no finish selector shown for this product.'),

                        Select::make('trustBadges')
                            ->label('Trust badges')
                            ->relationship('trustBadges', 'title')
                            ->multiple()
                            ->preload()
                            ->default(fn () => TrustBadge::query()->where('is_active', true)->pluck('id')->all()),
                    ]),
            ])->columns(3);
    }
}
