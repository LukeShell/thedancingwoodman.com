<?php

namespace App\Filament\Resources\Discounts\Tables;

use App\Enums\DiscountType;
use App\Models\Discount;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class DiscountsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('code')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('type')
                    ->badge()
                    ->formatStateUsing(fn (DiscountType $state): string => $state->label()),

                TextColumn::make('value')
                    ->label('Value')
                    ->state(fn (Discount $record): string => $record->type === DiscountType::Percentage
                        ? rtrim(rtrim(number_format((float) $record->value, 2), '0'), '.').'%'
                        : '£'.number_format((float) $record->value, 2)),

                TextColumn::make('times_used')
                    ->label('Used')
                    ->state(fn (Discount $record): string => $record->max_uses === null
                        ? (string) $record->times_used
                        : $record->times_used.' / '.$record->max_uses),

                TextColumn::make('starts_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('ends_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(),

                IconColumn::make('is_active')
                    ->boolean(),

                IconColumn::make('stackable')
                    ->boolean()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                TernaryFilter::make('is_active'),
                SelectFilter::make('type')
                    ->options(collect(DiscountType::cases())
                        ->mapWithKeys(fn (DiscountType $type) => [$type->value => $type->label()])
                        ->all()),
                Filter::make('currently_valid')
                    ->label('Currently valid')
                    ->query(fn (Builder $query) => $query
                        ->where('is_active', true)
                        ->where(fn (Builder $q) => $q->whereNull('starts_at')->orWhere('starts_at', '<=', now()))
                        ->where(fn (Builder $q) => $q->whereNull('ends_at')->orWhere('ends_at', '>=', now()))
                        ->where(fn (Builder $q) => $q->whereNull('max_uses')->orWhereColumn('times_used', '<', 'max_uses'))),
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
