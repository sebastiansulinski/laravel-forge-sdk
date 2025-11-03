<?php

use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use SebastianSulinski\LaravelForgeSdk\Actions\CreateCommand;
use SebastianSulinski\LaravelForgeSdk\Client;
use SebastianSulinski\LaravelForgeSdk\Payload\Server\CreateCommandPayload;

beforeEach(function () {
    config()->set('forge.token', 'test-token');
    config()->set('forge.timeout', 90);
    config()->set('forge.organisation', 'test-org');
});

it('creates a command on a site', function () {
    Http::fake([
        'forge.laravel.com/api/orgs/test-org/servers/123/sites/456/commands' => Http::response([
            'message' => 'The command has been queued for execution.',
        ], 202),
    ]);

    $client = app(Client::class);
    $action = new CreateCommand($client);

    $payload = new CreateCommandPayload(
        command: 'php artisan migrate'
    );

    $result = $action->handle(
        serverId: 123,
        siteId: 456,
        payload: $payload
    );

    expect($result)->toBeTrue();

    Http::assertSent(function (Request $request) {
        return $request->url() === 'https://forge.laravel.com/api/orgs/test-org/servers/123/sites/456/commands'
            && $request->method() === 'POST'
            && $request['command'] === 'php artisan migrate'
            && $request->hasHeader('Authorization', 'Bearer test-token')
            && $request->hasHeader('Accept', 'application/json')
            && $request->hasHeader('Content-Type', 'application/json');
    });
});

it('returns false when request fails', function () {
    Http::fake([
        'forge.laravel.com/api/orgs/test-org/servers/123/sites/456/commands' => Http::response([
            'message' => 'Server error',
        ], 500),
    ]);

    $client = app(Client::class);
    $action = new CreateCommand($client);

    $payload = new CreateCommandPayload(
        command: 'php artisan migrate'
    );

    $result = $action->handle(
        serverId: 123,
        siteId: 456,
        payload: $payload
    );

    expect($result)->toBeFalse();
});
