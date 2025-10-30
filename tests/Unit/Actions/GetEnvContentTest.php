<?php

use Illuminate\Http\Client\Request;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Http;
use SebastianSulinski\LaravelForgeSdk\Actions\GetEnvContent;
use SebastianSulinski\LaravelForgeSdk\Client;

beforeEach(function () {
    config()->set('forge.token', 'test-token');
    config()->set('forge.timeout', 90);
    config()->set('forge.organisation', 'test-org');
});

it('gets environment content', function () {
    Http::fake([
        'forge.laravel.com/api/orgs/test-org/servers/123/sites/456/environment' => Http::response([
            'data' => [
                'attributes' => [
                    'content' => implode(PHP_EOL, [
                        'APP_NAME=Laravel',
                        'APP_ENV=production',
                        'APP_KEY=base64:randomkey123',
                        'APP_DEBUG=false',
                        'APP_URL=https://example.com',
                        '',
                        'DB_CONNECTION=mysql',
                        'DB_HOST=127.0.0.1',
                        'DB_PORT=3306',
                        'DB_DATABASE=forge',
                        'DB_USERNAME=forge',
                        'DB_PASSWORD=password',
                    ]),
                ],
            ],
        ]),
    ]);

    $client = app(Client::class);
    $action = new GetEnvContent($client);

    $content = $action->handle(
        serverId: 123,
        siteId: 456
    );

    expect($content)->toContain('APP_NAME=Laravel')
        ->and($content)->toContain('APP_ENV=production')
        ->and($content)->toContain('APP_KEY=base64:randomkey123')
        ->and($content)->toContain('DB_CONNECTION=mysql')
        ->and($content)->toContain('DB_DATABASE=forge');

    Http::assertSent(function (Request $request) {
        return $request->url() === 'https://forge.laravel.com/api/orgs/test-org/servers/123/sites/456/environment'
            && $request->method() === 'GET'
            && $request->hasHeader('Authorization', 'Bearer test-token')
            && $request->hasHeader('Accept', 'application/json')
            && $request->hasHeader('Content-Type', 'application/json');
    });
});

it('returns empty string when content is missing', function () {
    Http::fake([
        'forge.laravel.com/api/orgs/test-org/servers/123/sites/456/environment' => Http::response([
            'data' => [
                'attributes' => [],
            ],
        ]),
    ]);

    $client = app(Client::class);
    $action = new GetEnvContent($client);

    $content = $action->handle(
        serverId: 123,
        siteId: 456
    );

    expect($content)->toBe('');
});

it('throws exception when request fails', function () {
    Http::fake([
        'forge.laravel.com/api/orgs/test-org/servers/123/sites/456/environment' => Http::response([
            'message' => 'Server error',
        ], 500),
    ]);

    $client = app(Client::class);
    $action = new GetEnvContent($client);

    $action->handle(
        serverId: 123,
        siteId: 456
    );
})->throws(RequestException::class);
