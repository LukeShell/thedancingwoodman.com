<?php

namespace App\Filament\Resources\Orders\RelationManagers;

use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ItemsRelationManager extends RelationManager
{
    protected static string $relationship = 'items';

    protected static ?string $title = 'Items';

    public function form(Schema $schema): Schema
    {
        return $schema->components([]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('product_name')
            ->columns([
                TextColumn::make('product_name')
                    ->label('Product')
                    ->searchable()
                    ->wrap(),

                TextColumn::make('variant_label')
                    ->label('Variant')
                    ->placeholder('—'),

                TextColumn::make('sku')
                    ->placeholder('—'),

                TextColumn::make('quantity')
                    ->alignRight(),

                TextColumn::make('unit_price')
                    ->label('Unit price')
                    ->money('GBP', divideBy: 100)
                    ->alignRight(),

                TextColumn::make('addons.name')
                    ->label('Add-ons')
                    ->badge()
                    ->separator(',')
                    ->placeholder('—'),

                TextColumn::make('line_total')
                    ->label('Line total')
                    ->money('GBP', divideBy: 100)
                    ->alignRight(),
            ])
            ->headerActions([])
            ->recordActions([])
            ->toolbarActions([]);
    }

    public function isReadOnly(): bool
    {
        return true;
    }
}
