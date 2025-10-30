<?php

use Illuminate\Http\Client\Request;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Http;
use SebastianSulinski\LaravelForgeSdk\Actions\UpdateEnvContent;
use SebastianSulinski\LaravelForgeSdk\Client;
use SebastianSulinski\LaravelForgeSdk\Payload\UpdateEnvContentPayload;

beforeEach(function () {
    config()->set('forge.token', 'test-token');
    config()->set('forge.timeout', 90);
    config()->set('forge.organisation', 'test-org');
});

it('updates environment content', function () {
    Http::fake([
        'forge.laravel.com/api/orgs/test-org/servers/123/sites/456/environment' => Http::response(),
    ]);

    $client = app(Client::class);
    $action = new UpdateEnvContent($client);

    $payload = new UpdateEnvContentPayload(
        environment: implode(PHP_EOL, [
            'APP_NAME=Laravel',
            'APP_ENV=production',
            'APP_KEY=base64:newkey456',
            'APP_DEBUG=false',
            'APP_URL=https://example.com',
            '',
            'DB_CONNECTION=mysql',
            'DB_HOST=127.0.0.1',
            'DB_PORT=3306',
            'DB_DATABASE=forge',
            'DB_USERNAME=forge',
            'DB_PASSWORD=secret',
        ]),
        cache: true,
        queues: true,
        encryption_key: 'base64:newkey456'
    );

    $action->handle(
        serverId: 123,
        siteId: 456,
        payload: $payload
    );

    Http::assertSent(function (Request $request) use ($payload) {
        return $request->url() === 'https://forge.laravel.com/api/orgs/test-org/servers/123/sites/456/environment'
            && $request->method() === 'PUT'
            && $request['environment'] === $payload->environment
            && $request['cache'] === true
            && $request['queues'] === true
            && $request['encryption_key'] === 'base64:newkey456'
            && $request->hasHeader('Authorization', 'Bearer test-token')
            && $request->hasHeader('Accept', 'application/json')
            && $request->hasHeader('Content-Type', 'application/json');
    });
});

it('updates environment content with minimal data', function () {
    Http::fake([
        'forge.laravel.com/api/orgs/test-org/servers/123/sites/456/environment' => Http::response(),
    ]);

    $client = app(Client::class);
    $action = new UpdateEnvContent($client);

    $payload = new UpdateEnvContentPayload(
        environment: 'APP_NAME=Laravel'
    );

    $action->handle(
        serverId: 123,
        siteId: 456,
        payload: $payload
    );

    Http::assertSent(function (Request $request) {
        return $request->url() === 'https://forge.laravel.com/api/orgs/test-org/servers/123/sites/456/environment'
            && $request->method() === 'PUT'
            && $request['environment'] === 'APP_NAME=Laravel'
            && ! isset($request['cache'])
            && ! isset($request['queues'])
            && ! isset($request['encryption_key'])
            && $request->hasHeader('Authorization', 'Bearer test-token')
            && $request->hasHeader('Accept', 'application/json')
            && $request->hasHeader('Content-Type', 'application/json');
    });
});

it('throws exception when request fails', function () {
    Http::fake([
        'forge.laravel.com/api/orgs/test-org/servers/123/sites/456/environment' => Http::response([
            'message' => 'Server error',
        ], 500),
    ]);

    $client = app(Client::class);
    $action = new UpdateEnvContent($client);

    $payload = new UpdateEnvContentPayload(
        environment: 'APP_NAME=Laravel'
    );

    $action->handle(
        serverId: 123,
        siteId: 456,
        payload: $payload
    );
})->throws(RequestException::class);
