<?php

use Illuminate\Http\Client\Request;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Http;
use SebastianSulinski\LaravelForgeSdk\Actions\CreateDatabaseSchema;
use SebastianSulinski\LaravelForgeSdk\Client;
use SebastianSulinski\LaravelForgeSdk\Payload\Database\CreateSchemaPayload;

beforeEach(function () {
    config()->set('forge.token', 'test-token');
    config()->set('forge.timeout', 90);
    config()->set('forge.organisation', 'test-org');
});

it('creates a database', function () {
    Http::fake([
        'forge.laravel.com/api/orgs/test-org/servers/123/database/schemas' => Http::response([
            'data' => [
                'id' => 456,
                'attributes' => [
                    'name' => 'my_database',
                    'status' => 'installing',
                    'created_at' => '2024-01-15T10:30:00.000000Z',
                    'updated_at' => '2024-01-15T10:30:00.000000Z',
                ],
            ],
        ], 201),
    ]);

    $client = app(Client::class);
    $action = new CreateDatabaseSchema($client);

    $payload = new CreateSchemaPayload(
        name: 'my_database',
        user: 'db_user',
        password: 'secret123'
    );

    $database = $action->handle(
        serverId: 123,
        payload: $payload
    );

    expect($database->id)->toBe(456)
        ->and($database->serverId)->toBe(123)
        ->and($database->name)->toBe('my_database')
        ->and($database->status->value)->toBe('installing');

    Http::assertSent(function (Request $request) {
        return $request->url() === 'https://forge.laravel.com/api/orgs/test-org/servers/123/database/schemas'
            && $request->method() === 'POST'
            && $request['name'] === 'my_database'
            && $request['user'] === 'db_user'
            && $request['password'] === 'secret123'
            && $request->hasHeader('Authorization', 'Bearer test-token')
            && $request->hasHeader('Accept', 'application/json')
            && $request->hasHeader('Content-Type', 'application/json');
    });
});

it('creates a database without optional user and password', function () {
    Http::fake([
        'forge.laravel.com/api/orgs/test-org/servers/123/database/schemas' => Http::response([
            'data' => [
                'id' => 456,
                'attributes' => [
                    'name' => 'my_database',
                    'status' => 'installing',
                    'created_at' => '2024-01-15T10:30:00.000000Z',
                    'updated_at' => '2024-01-15T10:30:00.000000Z',
                ],
            ],
        ], 201),
    ]);

    $client = app(Client::class);
    $action = new CreateDatabaseSchema($client);

    $payload = new CreateSchemaPayload(
        name: 'my_database'
    );

    $database = $action->handle(
        serverId: 123,
        payload: $payload
    );

    expect($database->name)->toBe('my_database');

    Http::assertSent(function (Request $request) {
        return $request->url() === 'https://forge.laravel.com/api/orgs/test-org/servers/123/database/schemas'
            && $request['name'] === 'my_database'
            && ! isset($request['user'])
            && ! isset($request['password']);
    });
});

it('throws exception when request fails', function () {
    Http::fake([
        'forge.laravel.com/api/orgs/test-org/servers/123/database/schemas' => Http::response([
            'message' => 'Server error',
        ], 500),
    ]);

    $client = app(Client::class);
    $action = new CreateDatabaseSchema($client);

    $payload = new CreateSchemaPayload(
        name: 'my_database'
    );

    $action->handle(
        serverId: 123,
        payload: $payload
    );
})->throws(RequestException::class);
