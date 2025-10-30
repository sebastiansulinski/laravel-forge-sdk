<?php

namespace SebastianSulinski\LaravelForgeSdk;

use Illuminate\Support\ServiceProvider;

class ForgeServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->mergeConfigFrom(
            __DIR__.'/../config/forge.php', 'forge'
        );

        $this->app->singleton(Client::class, function ($app) {
            return new Client(
                token: $app['config']->get('forge.token'),
                timeout: $app['config']->get('forge.timeout'),
                organisation: $app['config']->get('forge.organisation'),
            );
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../config/forge.php' => config_path('forge.php'),
            ], 'forge-config');
        }
    }
}
