<?php

use Illuminate\Http\Client\Request;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Http;
use SebastianSulinski\LaravelForgeSdk\Actions\GetDeploymentScript;
use SebastianSulinski\LaravelForgeSdk\Client;

beforeEach(function () {
    config()->set('forge.token', 'test-token');
    config()->set('forge.timeout', 90);
    config()->set('forge.organisation', 'test-org');
});

it('gets deployment script', function () {
    Http::fake([
        'forge.laravel.com/api/orgs/test-org/servers/123/sites/456/deployments/script' => Http::response([
            'data' => [
                'attributes' => [
                    'content' => implode(PHP_EOL, [
                        'cd /home/forge/example.com',
                        'git pull origin main',
                        'composer install --no-dev',
                        'php artisan migrate --force',
                    ]),
                ],
            ],
        ]),
    ]);

    $client = app(Client::class);
    $action = new GetDeploymentScript($client);

    $script = $action->handle(
        serverId: 123,
        siteId: 456
    );

    expect($script)->toContain('cd /home/forge/example.com')
        ->and($script)->toContain('git pull origin main')
        ->and($script)->toContain('composer install --no-dev')
        ->and($script)->toContain('php artisan migrate --force');

    Http::assertSent(function (Request $request) {
        return $request->url() === 'https://forge.laravel.com/api/orgs/test-org/servers/123/sites/456/deployments/script'
            && $request->method() === 'GET'
            && $request->hasHeader('Authorization', 'Bearer test-token')
            && $request->hasHeader('Accept', 'application/json')
            && $request->hasHeader('Content-Type', 'application/json');
    });
});

it('returns empty string when content is missing', function () {
    Http::fake([
        'forge.laravel.com/api/orgs/test-org/servers/123/sites/456/deployments/script' => Http::response([
            'data' => [
                'attributes' => [],
            ],
        ]),
    ]);

    $client = app(Client::class);
    $action = new GetDeploymentScript($client);

    $script = $action->handle(
        serverId: 123,
        siteId: 456
    );

    expect($script)->toBe('');
});

it('throws exception when request fails', function () {
    Http::fake([
        'forge.laravel.com/api/orgs/test-org/servers/123/sites/456/deployments/script' => Http::response([
            'message' => 'Server error',
        ], 500),
    ]);

    $client = app(Client::class);
    $action = new GetDeploymentScript($client);

    $action->handle(
        serverId: 123,
        siteId: 456
    );
})->throws(RequestException::class);
