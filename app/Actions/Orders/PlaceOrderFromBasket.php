<?php

namespace App\Actions\Orders;

use App\Enums\OrderStatus;
use App\Models\Basket;
use App\Models\BasketItem;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\ProductVariant;
use App\Payments\Contracts\PaymentIntentResult;
use App\Payments\Exceptions\PaymentException;
use App\Payments\PaymentManager;
use App\Support\ShippingResolver;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PlaceOrderFromBasket
{
    public function __construct(
        private readonly PaymentManager $payments,
        private readonly ShippingResolver $shipping,
    ) {}

    /**
     * @param  array<string, mixed>  $checkoutData
     * @return array{order: Order, intent: PaymentIntentResult}
     */
    public function __invoke(Basket $basket, array $checkoutData, ?string $gateway = null): array
    {
        if ($basket->isConverted()) {
            throw new PaymentException('This basket has already been converted to an order.');
        }

        /** @var Collection<int, BasketItem> $items */
        $items = $basket->items()
            ->with([
                'variant.product',
                'variant.attributeValues.attribute',
                'addons',
            ])
            ->get();

        if ($items->isEmpty()) {
            throw new PaymentException('Cannot place an order for an empty basket.');
        }

        $order = DB::transaction(function () use ($basket, $items, $checkoutData): Order {
            $currency = (string) config('payments.currency', 'GBP');

            $subtotal = 0;
            $lines = [];

            foreach ($items as $item) {
                $variant = $item->variant;
                $variantPriceMinor = (int) round(((float) $variant->price) * 100);
                $addonsTotalMinor = (int) $item->addons->sum(fn ($addon) => (int) round(((float) $addon->price) * 100));
                $unitPriceMinor = $variantPriceMinor + $addonsTotalMinor;
                $lineTotalMinor = $unitPriceMinor * $item->quantity;
                $subtotal += $lineTotalMinor;

                $lines[] = [
                    'item' => $item,
                    'variant' => $variant,
                    'unit_price' => $unitPriceMinor,
                    'line_total' => $lineTotalMinor,
                ];
            }

            $quote = $this->shipping->resolve(
                (string) $checkoutData['country'],
                (string) $checkoutData['postal_code'],
                $subtotal,
            );

            if ($quote === null) {
                throw new PaymentException('We are unable to ship to the address provided. Please review your shipping details.');
            }

            $order = Order::create([
                'reference' => $this->generateReference(),
                'basket_id' => $basket->id,
                'status' => OrderStatus::Pending,
                'currency' => $currency,
                'subtotal' => $subtotal,
                'shipping_total' => $quote->costPence,
                'shipping_zone_id' => $quote->zoneId,
                'shipping_method_name' => $quote->zoneName,
                'tax_total' => 0,
                'grand_total' => $subtotal + $quote->costPence,
                'email' => (string) $checkoutData['email'],
                'first_name' => (string) $checkoutData['first_name'],
                'last_name' => (string) $checkoutData['last_name'],
                'address_line_1' => (string) $checkoutData['address_line_1'],
                'address_line_2' => $checkoutData['address_line_2'] ?? null,
                'city' => (string) $checkoutData['city'],
                'country' => (string) $checkoutData['country'],
                'state' => $checkoutData['state'] ?? null,
                'postal_code' => (string) $checkoutData['postal_code'],
                'placed_at' => now(),
            ]);

            foreach ($lines as $line) {
                $variant = $line['variant'];

                $orderItem = OrderItem::create([
                    'order_id' => $order->id,
                    'product_variant_id' => $variant->id,
                    'product_name' => $variant->product->name,
                    'variant_label' => $this->variantLabel($variant) ?: $variant->sku,
                    'sku' => $variant->sku,
                    'unit_price' => $line['unit_price'],
                    'quantity' => $line['item']->quantity,
                    'line_total' => $line['line_total'],
                ]);

                foreach ($line['item']->addons as $addon) {
                    $orderItem->addons()->create([
                        'product_addon_id' => $addon->id,
                        'name' => $addon->name,
                        'price' => (int) round(((float) $addon->price) * 100),
                    ]);
                }
            }

            $basket->forceFill(['converted_at' => now()])->save();

            return $order->fresh();
        });

        $intent = $this->payments->driver($gateway)->createIntent($order);

        return ['order' => $order->fresh(), 'intent' => $intent];
    }

    private function generateReference(): string
    {
        return 'TDW-'.now()->format('Y').'-'.strtoupper(Str::random(8));
    }

    private function variantLabel(ProductVariant $variant): string
    {
        return $variant->attributeValues
            ->map(fn ($value) => $value->attribute->name.': '.$value->value)
            ->implode(' / ');
    }
}
