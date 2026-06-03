<?php

namespace App\Filament\Resources\ShippingZones\Schemas;

use App\Enums\ShippingMethodType;
use App\Models\ShippingZone;
use App\Support\Countries;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;

class ShippingZoneForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Zone')
                    ->columns(2)
                    ->schema([
                        TextInput::make('name')
                            ->required()
                            ->maxLength(255)
                            ->columnSpanFull(),

                        Select::make('country_code')
                            ->label('Country')
                            ->options([ShippingZone::ANY_COUNTRY => 'Any country (Rest of world)'] + Countries::list())
                            ->required()
                            ->searchable(),

                        TextInput::make('priority')
                            ->numeric()
                            ->minValue(0)
                            ->default(100)
                            ->required()
                            ->helperText('Lower numbers are matched first. Use lower values for specific zones (e.g. Highlands) and higher for catch-alls.'),

                        TagsInput::make('postcode_patterns')
                            ->label('Postcode patterns')
                            ->placeholder('e.g. BT*, IV1*, PO30 1HF*')
                            ->helperText('Use * as a wildcard. Leave empty to apply to the whole country.')
                            ->columnSpanFull(),

                        Toggle::make('is_active')
                            ->label('Active')
                            ->default(true)
                            ->columnSpanFull(),
                    ]),

                Section::make('Method')
                    ->columns(2)
                    ->schema([
                        Select::make('method_type')
                            ->label('Method type')
                            ->options(collect(ShippingMethodType::cases())->mapWithKeys(
                                fn (ShippingMethodType $case) => [$case->value => $case->label()]
                            )->all())
                            ->required()
                            ->live()
                            ->columnSpanFull(),

                        TextInput::make('flat_rate')
                            ->label('Flat rate (£)')
                            ->numeric()
                            ->minValue(0)
                            ->step(0.01)
                            ->prefix('£')
                            ->required(fn (Get $get): bool => $get('method_type') === ShippingMethodType::Flat->value)
                            ->visible(fn (Get $get): bool => $get('method_type') === ShippingMethodType::Flat->value)
                            ->dehydrateStateUsing(fn ($state) => $state === null || $state === '' ? null : (int) round(((float) $state) * 100))
                            ->formatStateUsing(fn ($state) => $state === null ? null : number_format(((int) $state) / 100, 2, '.', '')),

                        TextInput::make('free_min_subtotal')
                            ->label('Minimum order subtotal (£)')
                            ->helperText('Optional. Free shipping only applies when the basket subtotal meets this amount.')
                            ->numeric()
                            ->minValue(0)
                            ->step(0.01)
                            ->prefix('£')
                            ->visible(fn (Get $get): bool => $get('method_type') === ShippingMethodType::Free->value)
                            ->dehydrateStateUsing(fn ($state) => $state === null || $state === '' ? null : (int) round(((float) $state) * 100))
                            ->formatStateUsing(fn ($state) => $state === null ? null : number_format(((int) $state) / 100, 2, '.', '')),
                    ]),
            ])
            ->columns(1);
    }
}
