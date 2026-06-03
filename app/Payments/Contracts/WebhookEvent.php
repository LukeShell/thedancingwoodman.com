<?php

namespace App\Payments\Contracts;

readonly class WebhookEvent
{
    /**
     * @param  array<string, mixed>  $payload
     */
    public function __construct(
        public string $id,
        public string $type,
        public array $payload,
    ) {}
}
