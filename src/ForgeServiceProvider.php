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
    }
}
