<?php

namespace App\Support;

use App\Enums\ShippingMethodType;
use Livewire\Wireable;

final readonly class ShippingQuote implements Wireable
{
    public function __construct(
        public int $zoneId,
        public string $zoneName,
        public ShippingMethodType $methodType,
        public int $costPence,
    ) {}

    public function isFree(): bool
    {
        return $this->methodType === ShippingMethodType::Free;
    }

    public function formattedCost(): string
    {
        return '£'.number_format($this->costPence / 100, 2);
    }

    /**
     * @return array{zoneId: int, zoneName: string, methodType: string, costPence: int}
     */
    public function toLivewire(): array
    {
        return [
            'zoneId' => $this->zoneId,
            'zoneName' => $this->zoneName,
            'methodType' => $this->methodType->value,
            'costPence' => $this->costPence,
        ];
    }

    /**
     * @param  array{zoneId: int, zoneName: string, methodType: string, costPence: int}  $value
     */
    public static function fromLivewire($value): self
    {
        return new self(
            zoneId: (int) $value['zoneId'],
            zoneName: (string) $value['zoneName'],
            methodType: ShippingMethodType::from((string) $value['methodType']),
            costPence: (int) $value['costPence'],
        );
    }
}
