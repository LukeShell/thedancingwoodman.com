<?php

namespace App\Filament\Resources\TrustBadges\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class TrustBadgeForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('icon')
                    ->required()
                    ->options([
                        'truck' => 'Truck (delivery)',
                        'sparkles' => 'Sparkles (sustainability)',
                        'shield-check' => 'Shield (guarantee)',
                        'paint-brush' => 'Paint brush (hand-finished)',
                        'heart' => 'Heart',
                        'star' => 'Star',
                        'globe-europe-africa' => 'Globe (worldwide)',
                    ])
                    ->searchable(),

                TextInput::make('title')
                    ->required(),

                TextInput::make('subtitle')
                    ->required(),

                TextInput::make('sort_order')
                    ->required()
                    ->numeric()
                    ->default(0),

                Toggle::make('is_active')
                    ->default(true)
                    ->inline(false),
            ]);
    }
}
