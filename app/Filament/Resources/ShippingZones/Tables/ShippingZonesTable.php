<?php

namespace App\Filament\Resources\ShippingZones\Tables;

use App\Enums\ShippingMethodType;
use App\Models\ShippingZone;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class ShippingZonesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('country_code')
                    ->label('Country')
                    ->formatStateUsing(fn (string $state): string => $state === ShippingZone::ANY_COUNTRY ? 'Any' : $state),

                TextColumn::make('postcode_patterns')
                    ->label('Patterns')
                    ->formatStateUsing(function ($state): string {
                        $patterns = is_array($state) ? $state : [];

                        if ($patterns === []) {
                            return 'Whole country';
                        }

                        return count($patterns).' pattern'.(count($patterns) === 1 ? '' : 's');
                    }),

                TextColumn::make('priority')
                    ->sortable(),

                TextColumn::make('method_type')
                    ->label('Method')
                    ->badge()
                    ->color(fn (ShippingMethodType $state): string => match ($state) {
                        ShippingMethodType::Free => 'success',
                        ShippingMethodType::Flat => 'gray',
                    })
                    ->formatStateUsing(fn (ShippingMethodType $state): string => $state->label()),

                TextColumn::make('flat_rate')
                    ->label('Rate')
                    ->money('GBP', divideBy: 100)
                    ->placeholder('—'),

                IconColumn::make('is_active')
                    ->label('Active')
                    ->boolean(),
            ])
            ->defaultSort('priority')
            ->filters([
                SelectFilter::make('method_type')
                    ->options(collect(ShippingMethodType::cases())->mapWithKeys(
                        fn (ShippingMethodType $case) => [$case->value => $case->label()]
                    )->all()),

                TernaryFilter::make('is_active')
                    ->label('Active')
                    ->placeholder('All zones')
                    ->trueLabel('Active only')
                    ->falseLabel('Inactive only'),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
