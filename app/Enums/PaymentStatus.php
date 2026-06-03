<?php

namespace App\Enums;

enum PaymentStatus: string
{
    case Pending = 'pending';
    case Processing = 'processing';
    case RequiresAction = 'requires_action';
    case Succeeded = 'succeeded';
    case Failed = 'failed';
    case Cancelled = 'cancelled';
    case Refunded = 'refunded';
}
