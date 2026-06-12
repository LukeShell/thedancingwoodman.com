<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum AttributeDisplayType: string implements HasLabel
{
    case Dropdown = 'dropdown';
    case Buttons = 'buttons';

    public function getLabel(): string
    {
        return match ($this) {
            self::Dropdown => 'Dropdown',
            self::Buttons => 'Buttons',
        };
    }
}
