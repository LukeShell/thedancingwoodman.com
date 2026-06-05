<?php

namespace App\Filament\Resources\Finishes\Schemas;

use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class FinishForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
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

                ColorPicker::make('hex_color')
                    ->label('Swatch colour')
                    ->helperText('Used as the circular swatch on the product page when no swatch image is uploaded.'),

                SpatieMediaLibraryFileUpload::make('swatch')
                    ->label('Swatch image')
                    ->collection('swatch')
                    ->image()
                    ->imageEditor()
                    ->helperText('Optional grain image, displayed instead of the flat colour.'),

                Textarea::make('description')
                    ->columnSpanFull(),

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
