<?php

namespace App\Enums;

enum OrderStatus: string
{
    case Pending = 'pending';
    case AwaitingPayment = 'awaiting_payment';
    case Paid = 'paid';
    case Failed = 'failed';
    case Cancelled = 'cancelled';
    case Refunded = 'refunded';

    public function isTerminal(): bool
    {
        return match ($this) {
            self::Paid, self::Failed, self::Cancelled, self::Refunded => true,
            default => false,
        };
    }
}
