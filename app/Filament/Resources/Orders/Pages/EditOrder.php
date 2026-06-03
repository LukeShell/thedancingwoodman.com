<?php

namespace App\Filament\Resources\Orders\Pages;

use App\Enums\OrderStatus;
use App\Filament\Resources\Orders\OrderResource;
use App\Models\Order;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Filament\Support\Icons\Heroicon;

class EditOrder extends EditRecord
{
    protected static string $resource = OrderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('cancel')
                ->label('Mark as cancelled')
                ->icon(Heroicon::XCircle)
                ->color('danger')
                ->requiresConfirmation()
                ->modalHeading('Cancel this order?')
                ->modalDescription('The order status will move to Cancelled and the cancelled_at timestamp will be set.')
                ->visible(fn (Order $record): bool => ! $record->status->isTerminal())
                ->action(function (Order $record): void {
                    $record->forceFill([
                        'status' => OrderStatus::Cancelled,
                        'cancelled_at' => now(),
                    ])->save();

                    Notification::make()
                        ->title('Order cancelled')
                        ->success()
                        ->send();

                    $this->refreshFormData(['status', 'cancelled_at']);
                }),
        ];
    }
}
