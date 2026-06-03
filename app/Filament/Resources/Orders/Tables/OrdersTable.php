<?php

namespace App\Filament\Resources\Orders\Tables;

use App\Enums\OrderStatus;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class OrdersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('reference')
                    ->searchable()
                    ->copyable()
                    ->sortable(),

                TextColumn::make('customer')
                    ->label('Customer')
                    ->state(fn ($record): string => trim("{$record->first_name} {$record->last_name}"))
                    ->description(fn ($record): ?string => $record->email)
                    ->searchable(query: function (Builder $query, string $search): Builder {
                        return $query
                            ->where('first_name', 'like', "%{$search}%")
                            ->orWhere('last_name', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%");
                    }),

                TextColumn::make('status')
                    ->badge()
                    ->color(fn (OrderStatus $state): string => match ($state) {
                        OrderStatus::Paid => 'success',
                        OrderStatus::Pending, OrderStatus::AwaitingPayment => 'warning',
                        OrderStatus::Failed, OrderStatus::Cancelled => 'danger',
                        OrderStatus::Refunded => 'gray',
                    })
                    ->sortable(),

                TextColumn::make('grand_total')
                    ->label('Total')
                    ->money('GBP', divideBy: 100)
                    ->sortable(),

                TextColumn::make('items_count')
                    ->label('Items')
                    ->counts('items')
                    ->badge()
                    ->toggleable(),

                TextColumn::make('placed_at')
                    ->dateTime()
                    ->sortable(),

                TextColumn::make('paid_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('placed_at', 'desc')
            ->filters([
                SelectFilter::make('status')
                    ->options(collect(OrderStatus::cases())->mapWithKeys(
                        fn (OrderStatus $case) => [$case->value => ucfirst(str_replace('_', ' ', $case->value))]
                    )->all())
                    ->multiple(),

                TernaryFilter::make('paid')
                    ->placeholder('All orders')
                    ->trueLabel('Paid')
                    ->falseLabel('Unpaid')
                    ->queries(
                        true: fn (Builder $query) => $query->whereNotNull('paid_at'),
                        false: fn (Builder $query) => $query->whereNull('paid_at'),
                    ),

                Filter::make('placed_at')
                    ->schema([
                        DatePicker::make('placed_from')->label('Placed from'),
                        DatePicker::make('placed_until')->label('Placed until'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when($data['placed_from'] ?? null, fn (Builder $q, $date) => $q->whereDate('placed_at', '>=', $date))
                            ->when($data['placed_until'] ?? null, fn (Builder $q, $date) => $q->whereDate('placed_at', '<=', $date));
                    }),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([]);
    }
}
