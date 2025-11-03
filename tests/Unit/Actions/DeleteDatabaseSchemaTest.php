<?php

use Illuminate\Http\Client\Request;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Http;
use SebastianSulinski\LaravelForgeSdk\Actions\DeleteDatabaseSchema;
use SebastianSulinski\LaravelForgeSdk\Client;

beforeEach(function () {
    config()->set('forge.token', 'test-token');
    config()->set('forge.timeout', 90);
    config()->set('forge.organisation', 'test-org');
});

it('deletes a database schema', function () {
    Http::fake([
        'forge.laravel.com/api/orgs/test-org/servers/123/database/schemas/456' => Http::response(null, 202),
    ]);

    $client = app(Client::class);
    $action = new DeleteDatabaseSchema($client);

    $result = $action->handle(
        serverId: 123,
        databaseId: 456
    );

    expect($result)->toBeTrue();

    Http::assertSent(function (Request $request) {
        return $request->url() === 'https://forge.laravel.com/api/orgs/test-org/servers/123/database/schemas/456'
            && $request->method() === 'DELETE'
            && $request->hasHeader('Authorization', 'Bearer test-token')
            && $request->hasHeader('Accept', 'application/json')
            && $request->hasHeader('Content-Type', 'application/json');
    });
});

it('throws exception when request fails', function () {
    Http::fake([
        'forge.laravel.com/api/orgs/test-org/servers/123/database/schemas/456' => Http::response([
            'message' => 'Server error',
        ], 500),
    ]);

    $client = app(Client::class);
    $action = new DeleteDatabaseSchema($client);

    $action->handle(
        serverId: 123,
        databaseId: 456
    );
})->throws(RequestException::class);
