<?php

namespace SebastianSulinski\LaravelForgeSdk\Tests;

use Orchestra\Testbench\TestCase as Orchestra;
use SebastianSulinski\LaravelForgeSdk\ForgeServiceProvider;

abstract class TestCase extends Orchestra
{
    /**
     * Get package providers.
     *
     * @param  \Illuminate\Foundation\Application  $app
     * @return array<int, class-string>
     */
    protected function getPackageProviders($app): array
    {
        return [
            ForgeServiceProvider::class,
        ];
    }

    /**
     * Define environment setup.
     *
     * @param  \Illuminate\Foundation\Application  $app
     */
    protected function getEnvironmentSetUp($app): void
    {
        $app['config']->set('forge.token', 'test-token');
        $app['config']->set('forge.timeout', 60);
        $app['config']->set('forge.organisation', 'test-org');
    }
}
