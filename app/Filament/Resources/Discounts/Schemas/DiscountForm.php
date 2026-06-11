<?php

namespace App\Filament\Resources\Discounts\Schemas;

use App\Enums\DiscountType;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;

class DiscountForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Code')
                    ->columns(2)
                    ->columnSpan(2)
                    ->schema([
                        TextInput::make('code')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(64)
                            ->helperText('Customers enter this exact code at checkout. Stored uppercase.')
                            ->dehydrateStateUsing(fn (?string $state): ?string => $state !== null ? strtoupper(trim($state)) : null),

                        TextInput::make('description')
                            ->maxLength(255)
                            ->columnSpanFull(),
                    ]),

                Section::make('Discount')
                    ->columns(2)
                    ->columnSpan(2)
                    ->schema([
                        Select::make('type')
                            ->options(collect(DiscountType::cases())
                                ->mapWithKeys(fn (DiscountType $type) => [$type->value => $type->label()])
                                ->all())
                            ->required()
                            ->live()
                            ->default(DiscountType::Percentage->value),

                        TextInput::make('value')
                            ->required()
                            ->numeric()
                            ->minValue(0)
                            ->prefix(fn (Get $get): string => $get('type') === DiscountType::Fixed->value ? '£' : '')
                            ->suffix(fn (Get $get): string => $get('type') === DiscountType::Percentage->value ? '%' : '')
                            ->helperText(fn (Get $get): string => $get('type') === DiscountType::Percentage->value
                                ? 'Percentage off eligible items (0–100).'
                                : 'Fixed pound amount off eligible items.'),

                        TextInput::make('min_subtotal')
                            ->label('Minimum eligible subtotal')
                            ->numeric()
                            ->prefix('£')
                            ->minValue(0)
                            ->helperText('Leave blank for no minimum.'),

                        TextInput::make('max_uses')
                            ->label('Total usage limit')
                            ->numeric()
                            ->minValue(1)
                            ->helperText('Leave blank for unlimited.'),
                    ]),

                Section::make('Availability')
                    ->columns(2)
                    ->columnSpan(2)
                    ->schema([
                        DateTimePicker::make('starts_at')->seconds(false),
                        DateTimePicker::make('ends_at')->seconds(false),

                        Toggle::make('is_active')
                            ->default(true)
                            ->inline(false),

                        Toggle::make('stackable')
                            ->default(false)
                            ->inline(false)
                            ->helperText('Reserved for future use. Only one code can be applied per basket today regardless of this flag.'),
                    ]),

                Section::make('Exclusions')
                    ->description('Products or categories listed here are excluded from this discount — their line totals will not count towards the discount.')
                    ->columnSpan(2)
                    ->schema([
                        Select::make('excludedProducts')
                            ->label('Excluded products')
                            ->relationship('excludedProducts', 'name')
                            ->multiple()
                            ->preload()
                            ->searchable(),

                        Select::make('excludedCategories')
                            ->label('Excluded categories')
                            ->relationship('excludedCategories', 'name')
                            ->multiple()
                            ->preload()
                            ->searchable(),
                    ]),
            ])->columns(2);
    }
}
