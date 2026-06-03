<?php

namespace App\Providers;

use App\Payments\Contracts\PaymentGateway;
use App\Payments\PaymentManager;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\ServiceProvider;

class PaymentServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(PaymentManager::class, function (Application $app): PaymentManager {
            $manager = new PaymentManager((string) config('payments.default', 'stripe'));

            foreach ((array) config('payments.gateways', []) as $key => $config) {
                if (! ($config['enabled'] ?? false)) {
                    continue;
                }

                $driver = $config['driver'] ?? null;

                if (! is_string($driver) || ! class_exists($driver)) {
                    continue;
                }

                /** @var PaymentGateway $instance */
                $instance = $app->make($driver, ['config' => $config]);
                $manager->register($instance);
            }

            return $manager;
        });
    }
}
