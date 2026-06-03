<?php

namespace App\Support;

class Countries
{
    /**
     * @return array<string, string>
     */
    public static function list(): array
    {
        return [
            'GB' => 'United Kingdom',
            'US' => 'United States',
        ];
    }

    public static function has(string $code): bool
    {
        return array_key_exists($code, self::list());
    }
}
