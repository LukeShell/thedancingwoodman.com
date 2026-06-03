document.addEventListener('alpine:init', () => {
    window.Alpine.data('stripePaymentElement', (options = {}) => ({
        stripe: null,
        elements: null,
        paymentElement: null,
        publishableKey: options.publishableKey || '',
        currency: (options.currency || 'gbp').toLowerCase(),
        amount: 0,
        ready: false,
        processing: false,
        error: null,

        init() {
            if (! window.Stripe) {
                this.error = 'Stripe.js failed to load. Please refresh and try again.';
                return;
            }

            if (! this.publishableKey) {
                this.error = 'Stripe is not configured. Please contact support.';
                return;
            }

            this.amount = Number(this.$wire.get('grandTotalPence')) || 0;

            if (this.amount <= 0) {
                this.error = 'No items in basket.';
                return;
            }

            try {
                this.stripe = window.Stripe(this.publishableKey);
                this.elements = this.stripe.elements({
                    mode: 'payment',
                    amount: this.amount,
                    currency: this.currency,
                    appearance: { theme: 'stripe' },
                });
                this.paymentElement = this.elements.create('payment');
                this.paymentElement.mount('#stripe-payment-element');
                this.ready = true;
            } catch (e) {
                this.error = e.message || 'Failed to load payment form.';
                return;
            }

            this.$wire.$watch('grandTotalPence', (newAmount) => {
                const next = Number(newAmount) || 0;
                if (next <= 0 || next === this.amount || ! this.elements) {
                    return;
                }
                this.amount = next;
                try {
                    this.elements.update({ amount: next });
                } catch (e) {
                    this.error = e.message || 'Failed to update payment amount.';
                }
            });
        },

        async onPrimaryClick() {
            if (this.processing || ! this.ready) {
                return;
            }

            this.processing = true;
            this.error = null;

            try {
                const { error: submitError } = await this.elements.submit();
                if (submitError) {
                    this.error = submitError.message || 'Please check your card details.';
                    this.processing = false;
                    return;
                }

                let intent = null;
                const off = this.$wire.on('payment-intent-created', (payload) => {
                    intent = Array.isArray(payload) ? payload[0] : payload;
                });

                await this.$wire.placeOrder();

                if (typeof off === 'function') {
                    off();
                }

                if (! intent) {
                    this.processing = false;
                    return;
                }

                const { error } = await this.stripe.confirmPayment({
                    elements: this.elements,
                    clientSecret: intent.clientSecret,
                    confirmParams: { return_url: intent.returnUrl },
                });

                if (error) {
                    this.error = error.message || 'Payment failed.';
                    this.processing = false;
                }
            } catch (e) {
                this.error = e.message || 'Unexpected error confirming payment.';
                this.processing = false;
            }
        },
    }));
});
