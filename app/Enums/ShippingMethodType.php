<?php

namespace App\Enums;

enum ShippingMethodType: string
{
    case Free = 'free';
    case Flat = 'flat';

    public function label(): string
    {
        return match ($this) {
            self::Free => 'Free shipping',
            self::Flat => 'Flat rate',
        };
    }
}
