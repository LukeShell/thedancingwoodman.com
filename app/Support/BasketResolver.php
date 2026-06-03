<?php

namespace App\Support;

use App\Models\Basket;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Str;

class BasketResolver
{
    private const COOKIE_NAME = 'basket_token';

    private const COOKIE_LIFETIME_MINUTES = 60 * 24 * 365;

    private ?Basket $cached = null;

    public function current(): Basket
    {
        if ($this->cached !== null) {
            return $this->cached;
        }

        $token = request()->cookie(self::COOKIE_NAME);

        if (is_string($token) && $token !== '') {
            $basket = Basket::query()->where('token', $token)->first();

            if ($basket) {
                return $this->cached = $basket;
            }
        }

        $newToken = Str::random(40);
        $basket = Basket::create(['token' => $newToken]);

        Cookie::queue(self::COOKIE_NAME, $newToken, self::COOKIE_LIFETIME_MINUTES);

        return $this->cached = $basket;
    }

    public function forget(): void
    {
        $this->cached = null;
    }
}
