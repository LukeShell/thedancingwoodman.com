<?php

namespace App\Filament\Resources\Orders\Schemas;

use App\Enums\OrderStatus;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class OrderForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Summary')
                    ->columns(2)
                    ->columnSpan(2)
                    ->schema([
                        TextEntry::make('reference')
                            ->copyable(),

                        TextEntry::make('status')
                            ->badge()
                            ->color(fn (OrderStatus $state): string => match ($state) {
                                OrderStatus::Paid => 'success',
                                OrderStatus::Pending, OrderStatus::AwaitingPayment => 'warning',
                                OrderStatus::Failed, OrderStatus::Cancelled => 'danger',
                                OrderStatus::Refunded => 'gray',
                            }),

                        TextEntry::make('subtotal')
                            ->money('GBP', divideBy: 100),

                        TextEntry::make('shipping_total')
                            ->label('Shipping')
                            ->money('GBP', divideBy: 100),

                        TextEntry::make('tax_total')
                            ->label('Tax')
                            ->money('GBP', divideBy: 100),

                        TextEntry::make('grand_total')
                            ->label('Total')
                            ->money('GBP', divideBy: 100)
                            ->weight('bold'),

                        TextEntry::make('placed_at')
                            ->dateTime(),

                        TextEntry::make('paid_at')
                            ->dateTime()
                            ->placeholder('—'),

                        TextEntry::make('cancelled_at')
                            ->dateTime()
                            ->placeholder('—'),
                    ]),

                Section::make('Customer')
                    ->columnSpan(1)
                    ->schema([
                        TextInput::make('email')
                            ->disabled(),
                        TextInput::make('first_name')
                            ->disabled(),
                        TextInput::make('last_name')
                            ->disabled(),
                    ]),

                Section::make('Shipping address')
                    ->columns(2)
                    ->columnSpan(3)
                    ->schema([
                        TextInput::make('address_line_1')
                            ->label('Address line 1')
                            ->required()
                            ->columnSpanFull(),

                        TextInput::make('address_line_2')
                            ->label('Address line 2')
                            ->columnSpanFull(),

                        TextInput::make('city')
                            ->required()
                            ->maxLength(100),

                        TextInput::make('state')
                            ->label('State / county')
                            ->maxLength(100),

                        TextInput::make('postal_code')
                            ->label('Postcode')
                            ->required()
                            ->maxLength(20),

                        TextInput::make('country')
                            ->label('Country (ISO 3166)')
                            ->required()
                            ->maxLength(2)
                            ->minLength(2),
                    ]),

                Section::make('Internal notes')
                    ->columnSpan(3)
                    ->schema([
                        Textarea::make('internal_notes')
                            ->label('')
                            ->rows(4)
                            ->placeholder('Notes are visible to staff only.')
                            ->columnSpanFull(),
                    ]),
            ])
            ->columns(3);
    }
}
