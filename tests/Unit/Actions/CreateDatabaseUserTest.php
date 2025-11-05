<?php

use Illuminate\Http\Client\Request;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Http;
use SebastianSulinski\LaravelForgeSdk\Actions\CreateDatabaseUser;
use SebastianSulinski\LaravelForgeSdk\Client;
use SebastianSulinski\LaravelForgeSdk\Payload\Database\CreateUserPayload;

beforeEach(function () {
    config()->set('forge.token', 'test-token');
    config()->set('forge.timeout', 90);
    config()->set('forge.organisation', 'test-org');
});

it('creates a database user', function () {
    Http::fake([
        'forge.laravel.com/api/orgs/test-org/servers/123/database/users' => Http::response([
            'data' => [
                'id' => 789,
                'attributes' => [
                    'name' => 'james',
                    'status' => 'installing',
                    'created_at' => '2024-01-15T10:30:00.000000Z',
                    'updated_at' => '2024-01-15T10:30:00.000000Z',
                ],
            ],
        ], 202),
    ]);

    $client = app(Client::class);
    $action = new CreateDatabaseUser($client);

    $payload = new CreateUserPayload(
        name: 'james',
        password: 'secret123'
    );

    $databaseUser = $action->handle(
        serverId: 123,
        payload: $payload
    );

    expect($databaseUser->id)->toBe(789)
        ->and($databaseUser->serverId)->toBe(123)
        ->and($databaseUser->name)->toBe('james')
        ->and($databaseUser->status->value)->toBe('installing');

    Http::assertSent(function (Request $request) {
        return $request->url() === 'https://forge.laravel.com/api/orgs/test-org/servers/123/database/users'
            && $request->method() === 'POST'
            && $request['name'] === 'james'
            && $request['password'] === 'secret123'
            && $request->hasHeader('Authorization', 'Bearer test-token')
            && $request->hasHeader('Accept', 'application/json')
            && $request->hasHeader('Content-Type', 'application/json');
    });
});

it('creates a database user with read-only access', function () {
    Http::fake([
        'forge.laravel.com/api/orgs/test-org/servers/123/database/users' => Http::response([
            'data' => [
                'id' => 789,
                'attributes' => [
                    'name' => 'readonly_user',
                    'status' => 'installing',
                    'created_at' => '2024-01-15T10:30:00.000000Z',
                    'updated_at' => '2024-01-15T10:30:00.000000Z',
                ],
            ],
        ], 202),
    ]);

    $client = app(Client::class);
    $action = new CreateDatabaseUser($client);

    $payload = new CreateUserPayload(
        name: 'readonly_user',
        password: 'secret123',
        readOnly: true
    );

    $databaseUser = $action->handle(
        serverId: 123,
        payload: $payload
    );

    expect($databaseUser->name)->toBe('readonly_user');

    Http::assertSent(function (Request $request) {
        return $request->url() === 'https://forge.laravel.com/api/orgs/test-org/servers/123/database/users'
            && $request['name'] === 'readonly_user'
            && $request['password'] === 'secret123'
            && $request['read_only'] === true;
    });
});

it('creates a database user with assigned databases', function () {
    Http::fake([
        'forge.laravel.com/api/orgs/test-org/servers/123/database/users' => Http::response([
            'data' => [
                'id' => 789,
                'attributes' => [
                    'name' => 'app_user',
                    'status' => 'installing',
                    'created_at' => '2024-01-15T10:30:00.000000Z',
                    'updated_at' => '2024-01-15T10:30:00.000000Z',
                ],
            ],
        ], 202),
    ]);

    $client = app(Client::class);
    $action = new CreateDatabaseUser($client);

    $payload = new CreateUserPayload(
        name: 'app_user',
        password: 'secret123',
        databaseIds: [456, 457]
    );

    $databaseUser = $action->handle(
        serverId: 123,
        payload: $payload
    );

    expect($databaseUser->name)->toBe('app_user');

    Http::assertSent(function (Request $request) {
        return $request->url() === 'https://forge.laravel.com/api/orgs/test-org/servers/123/database/users'
            && $request['name'] === 'app_user'
            && $request['password'] === 'secret123'
            && $request['database_ids'] === [456, 457];
    });
});

it('creates a database user with all options', function () {
    Http::fake([
        'forge.laravel.com/api/orgs/test-org/servers/123/database/users' => Http::response([
            'data' => [
                'id' => 789,
                'attributes' => [
                    'name' => 'full_user',
                    'status' => 'installing',
                    'created_at' => '2024-01-15T10:30:00.000000Z',
                    'updated_at' => '2024-01-15T10:30:00.000000Z',
                ],
            ],
        ], 202),
    ]);

    $client = app(Client::class);
    $action = new CreateDatabaseUser($client);

    $payload = new CreateUserPayload(
        name: 'full_user',
        password: 'secret123',
        readOnly: true,
        databaseIds: [456, 457, 458]
    );

    $databaseUser = $action->handle(
        serverId: 123,
        payload: $payload
    );

    expect($databaseUser->name)->toBe('full_user');

    Http::assertSent(function (Request $request) {
        return $request->url() === 'https://forge.laravel.com/api/orgs/test-org/servers/123/database/users'
            && $request['name'] === 'full_user'
            && $request['password'] === 'secret123'
            && $request['read_only'] === true
            && $request['database_ids'] === [456, 457, 458];
    });
});

it('throws exception when request fails', function () {
    Http::fake([
        'forge.laravel.com/api/orgs/test-org/servers/123/database/users' => Http::response([
            'message' => 'Server error',
        ], 500),
    ]);

    $client = app(Client::class);
    $action = new CreateDatabaseUser($client);

    $payload = new CreateUserPayload(
        name: 'james',
        password: 'secret123'
    );

    $action->handle(
        serverId: 123,
        payload: $payload
    );
})->throws(RequestException::class);
