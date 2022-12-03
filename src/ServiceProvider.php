<?php

namespace Polygontech\NagadDisbursement;

use Illuminate\Support\ServiceProvider as BaseServiceProvider;
use Illuminate\Contracts\Support\DeferrableProvider;

/**
 * @internal
 */
class ServiceProvider extends BaseServiceProvider implements DeferrableProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            __DIR__ . '/../config/config.php' => config_path('nagad.php'),
        ]);

        $this->app->singleton(NagadDisbursement::class, function ($app) {
            $config = $app['config']['nagad']['disbursement'];
            $client = new Client(new Config(
                baseUrl: $config['base_url'],
                merchantAggegatorId: $config['merchant_aggegator_id'],
                merchantId: $config['merchant_id'],
                pgPublicKey: $config['pg_public_key'],
                merchantPrivateKey: $config['merchant_private_key'],
                hmacKey: $config['hmac_key']
            ));
            return new NagadDisbursement($client);
        });

        $this->app->bind("nagad.disbursement", function ($app) {
            return $app->make(NagadDisbursement::class);
        });
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return [NagadDisbursement::class, "nagad.disbursement"];
    }
}
