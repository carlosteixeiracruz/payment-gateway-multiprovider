<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\Payments\PaymentProcessor;
use App\Services\Payments\Providers\StripeProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(PaymentProcessor::class, function ($app) {
            $processor = new PaymentProcessor();

            // Note que aqui registramos o provedor
            $processor->addProvider('stripe', new StripeProvider());

            return $processor;
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
