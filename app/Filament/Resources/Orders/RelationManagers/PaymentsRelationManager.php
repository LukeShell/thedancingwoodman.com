<?php

namespace App\Filament\Resources\Orders\RelationManagers;

use App\Enums\PaymentStatus;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class PaymentsRelationManager extends RelationManager
{
    protected static string $relationship = 'payments';

    protected static ?string $title = 'Payments';

    public function form(Schema $schema): Schema
    {
        return $schema->components([]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('gateway_payment_id')
            ->columns([
                TextColumn::make('gateway')
                    ->badge(),

                TextColumn::make('gateway_payment_id')
                    ->label('Gateway ref')
                    ->copyable()
                    ->limit(20)
                    ->placeholder('—'),

                TextColumn::make('status')
                    ->badge()
                    ->color(fn (PaymentStatus $state): string => match ($state) {
                        PaymentStatus::Succeeded => 'success',
                        PaymentStatus::Pending, PaymentStatus::Processing, PaymentStatus::RequiresAction => 'warning',
                        PaymentStatus::Failed, PaymentStatus::Cancelled => 'danger',
                        PaymentStatus::Refunded => 'gray',
                    }),

                TextColumn::make('amount')
                    ->money('GBP', divideBy: 100)
                    ->alignRight(),

                TextColumn::make('payment_method_type')
                    ->label('Method')
                    ->placeholder('—'),

                TextColumn::make('processed_at')
                    ->dateTime()
                    ->placeholder('—'),

                TextColumn::make('last_error')
                    ->wrap()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('created_at', 'desc')
            ->headerActions([])
            ->recordActions([])
            ->toolbarActions([]);
    }

    public function isReadOnly(): bool
    {
        return true;
    }
}
