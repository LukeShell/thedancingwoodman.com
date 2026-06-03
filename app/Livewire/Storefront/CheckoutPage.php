<?php

namespace App\Livewire\Storefront;

use App\Actions\Orders\PlaceOrderFromBasket;
use App\Models\Basket;
use App\Models\BasketItem;
use App\Payments\Exceptions\PaymentException;
use App\Payments\PaymentManager;
use App\Support\BasketResolver;
use App\Support\Countries;
use App\Support\ShippingQuote;
use App\Support\ShippingResolver;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.storefront')]
#[Title('Checkout')]
class CheckoutPage extends Component
{
    public Basket $basket;

    public string $email = '';

    public string $firstName = '';

    public string $lastName = '';

    public string $addressLine1 = '';

    public string $addressLine2 = '';

    public string $city = '';

    public string $country = 'GB';

    public string $state = '';

    public string $postalCode = '';

    public string $selectedGateway = 'stripe';

    public ?string $placementError = null;

    public ?ShippingQuote $shippingQuote = null;

    public ?string $shippingError = null;

    public int $grandTotalPence = 0;

    public function mount(BasketResolver $resolver)
    {
        $this->basket = $resolver->current();

        if ($this->basket->isConverted()) {
            $order = $this->basket->order;

            if ($order !== null) {
                return $this->redirectRoute('checkout.confirm', ['order' => $order->reference], navigate: true);
            }

            return $this->redirectRoute('home', navigate: true);
        }

        if ($this->basket->items()->count() === 0) {
            return $this->redirectRoute('basket.show', navigate: true);
        }

        $this->email = (string) ($this->basket->email ?? '');
        $this->firstName = (string) ($this->basket->first_name ?? '');
        $this->lastName = (string) ($this->basket->last_name ?? '');
        $this->addressLine1 = (string) ($this->basket->address_line_1 ?? '');
        $this->addressLine2 = (string) ($this->basket->address_line_2 ?? '');
        $this->city = (string) ($this->basket->city ?? '');
        $this->country = (string) ($this->basket->country ?? 'GB');
        $this->state = (string) ($this->basket->state ?? '');
        $this->postalCode = (string) ($this->basket->postal_code ?? '');

        $this->refreshShipping();
    }

    /**
     * @return array<string, mixed>
     */
    protected function rules(): array
    {
        return [
            'email' => ['required', 'email:rfc', 'max:255'],
            'firstName' => ['required', 'string', 'max:100'],
            'lastName' => ['required', 'string', 'max:100'],
            'addressLine1' => ['required', 'string', 'max:255'],
            'addressLine2' => ['nullable', 'string', 'max:255'],
            'city' => ['required', 'string', 'max:100'],
            'country' => ['required', Rule::in(array_keys(Countries::list()))],
            'state' => ['nullable', 'string', 'max:100'],
            'postalCode' => ['required', 'string', 'max:20'],
        ];
    }

    /**
     * @var array<string, string>
     */
    private const FIELD_COLUMN_MAP = [
        'email' => 'email',
        'firstName' => 'first_name',
        'lastName' => 'last_name',
        'addressLine1' => 'address_line_1',
        'addressLine2' => 'address_line_2',
        'city' => 'city',
        'country' => 'country',
        'state' => 'state',
        'postalCode' => 'postal_code',
    ];

    public function updated(string $name, mixed $value): void
    {
        if (! array_key_exists($name, self::FIELD_COLUMN_MAP)) {
            return;
        }

        $this->validateOnly($name);

        $normalized = is_string($value) && trim($value) === '' ? null : $value;

        $this->basket->update([self::FIELD_COLUMN_MAP[$name] => $normalized]);

        if (in_array($name, ['country', 'postalCode'], true)) {
            $this->refreshShipping();
        }
    }

    public function refreshShipping(?ShippingResolver $resolver = null): void
    {
        $resolver ??= app(ShippingResolver::class);

        $this->shippingError = null;
        $this->shippingQuote = null;

        $subtotal = $this->subtotalPence();

        if ($this->country !== '' && trim($this->postalCode) !== '') {
            $quote = $resolver->resolve($this->country, $this->postalCode, $subtotal);

            if ($quote === null) {
                $this->shippingError = __('We can\'t ship to this address yet. Please check your country and postcode.');
            } else {
                $this->shippingQuote = $quote;
            }
        }

        $this->grandTotalPence = $subtotal + ($this->shippingQuote?->costPence ?? 0);
    }

    private function subtotalPence(): int
    {
        return (int) $this->basket->items()
            ->with(['variant', 'addons'])
            ->get()
            ->sum(fn (BasketItem $item) => (int) round(((float) $item->lineTotal()) * 100));
    }

    public function placeOrder(PaymentManager $manager, PlaceOrderFromBasket $action): void
    {
        $this->placementError = null;

        $data = $this->validate();

        $this->refreshShipping();

        if ($this->shippingQuote === null) {
            $this->placementError = $this->shippingError ?? __('Shipping is unavailable for this address.');

            return;
        }

        $checkout = [
            'email' => $data['email'],
            'first_name' => $data['firstName'],
            'last_name' => $data['lastName'],
            'address_line_1' => $data['addressLine1'],
            'address_line_2' => $data['addressLine2'] ?: null,
            'city' => $data['city'],
            'country' => $data['country'],
            'state' => $data['state'] ?: null,
            'postal_code' => $data['postalCode'],
        ];

        try {
            $result = $action($this->basket, $checkout, $this->selectedGateway);
        } catch (PaymentException $e) {
            $this->placementError = $e->getMessage();

            return;
        }

        $this->dispatch(
            'payment-intent-created',
            gateway: $this->selectedGateway,
            clientSecret: $result['intent']->clientSecret,
            publishableKey: $result['intent']->publishableKey,
            returnUrl: route('checkout.confirm', ['order' => $result['order']->reference]),
        );
    }

    public function render()
    {
        $items = $this->basket->items()
            ->with([
                'variant.product.media',
                'variant.attributeValues.attribute',
                'addons',
            ])
            ->get();

        $subtotal = $items->sum(fn (BasketItem $item) => (float) $item->lineTotal());

        $shippingCost = $this->shippingQuote !== null ? $this->shippingQuote->costPence / 100 : 0.0;
        $grandTotal = $subtotal + $shippingCost;

        return view('livewire.storefront.checkout-page', [
            'items' => $items,
            'subtotal' => $subtotal,
            'shippingCost' => $shippingCost,
            'grandTotal' => $grandTotal,
            'countries' => Countries::list(),
        ]);
    }
}
