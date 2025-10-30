<?php

use Illuminate\Http\Client\Request;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Http;
use SebastianSulinski\LaravelForgeSdk\Actions\FetchAllPages;
use SebastianSulinski\LaravelForgeSdk\Actions\ListDatabaseUsers;
use SebastianSulinski\LaravelForgeSdk\Client;
use SebastianSulinski\LaravelForgeSdk\Payload\ListDatabaseUsersPayload;

beforeEach(function () {
    config()->set('forge.token', 'test-token');
    config()->set('forge.timeout', 90);
    config()->set('forge.organisation', 'test-org');
});

it('lists database users', function () {
    Http::fake([
        'forge.laravel.com/api/orgs/test-org/servers/123/database/users*' => Http::response([
            'data' => [
                [
                    'id' => 1,
                    'attributes' => [
                        'name' => 'forge_user',
                        'status' => 'installed',
                        'created_at' => '2024-01-15T10:30:00.000000Z',
                        'updated_at' => '2024-01-15T10:30:00.000000Z',
                    ],
                ],
                [
                    'id' => 2,
                    'attributes' => [
                        'name' => 'app_user',
                        'status' => 'installed',
                        'created_at' => '2024-01-16T10:30:00.000000Z',
                        'updated_at' => '2024-01-16T10:30:00.000000Z',
                    ],
                ],
            ],
            'links' => [
                'first' => 'https://forge.laravel.com/api/orgs/test-org/servers/123/database/users?page[size]=20',
                'last' => 'https://forge.laravel.com/api/orgs/test-org/servers/123/database/users?page[size]=20',
            ],
            'meta' => [
                'current_page' => 1,
                'from' => 1,
                'last_page' => 1,
                'per_page' => 20,
                'to' => 2,
                'total' => 2,
            ],
        ]),
    ]);

    $client = app(Client::class);
    $fetchAllPages = new FetchAllPages($client);
    $action = new ListDatabaseUsers($client, $fetchAllPages);

    $users = $action->handle(
        serverId: 123,
        payload: new ListDatabaseUsersPayload(
            filterName: 'forge',
            filterStatus: 'installed'
        )
    );

    expect($users)->toHaveCount(2)
        ->and($users->first()->id)->toBe(1)
        ->and($users->first()->serverId)->toBe(123)
        ->and($users->first()->name)->toBe('forge_user')
        ->and($users->first()->status->value)->toBe('installed')
        ->and($users->last()->id)->toBe(2)
        ->and($users->last()->name)->toBe('app_user');

    Http::assertSent(function (Request $request) {
        return str_contains($request->url(), 'https://forge.laravel.com/api/orgs/test-org/servers/123/database/users')
            && $request->method() === 'GET'
            && $request->hasHeader('Authorization', 'Bearer test-token')
            && $request->hasHeader('Accept', 'application/json')
            && $request->hasHeader('Content-Type', 'application/json');
    });
});

it('returns empty collection when no users found', function () {
    Http::fake([
        'forge.laravel.com/api/orgs/test-org/servers/123/database/users*' => Http::response([
            'data' => [],
            'links' => [
                'first' => 'https://forge.laravel.com/api/orgs/test-org/servers/123/database/users?page[size]=20',
                'last' => 'https://forge.laravel.com/api/orgs/test-org/servers/123/database/users?page[size]=20',
            ],
            'meta' => [
                'current_page' => 1,
                'from' => null,
                'last_page' => 1,
                'per_page' => 20,
                'to' => null,
                'total' => 0,
            ],
        ]),
    ]);

    $client = app(Client::class);
    $fetchAllPages = new FetchAllPages($client);
    $action = new ListDatabaseUsers($client, $fetchAllPages);

    $users = $action->handle(
        serverId: 123
    );

    expect($users)->toBeEmpty();
});

it('throws exception when request fails', function () {
    Http::fake([
        'forge.laravel.com/api/orgs/test-org/servers/123/database/users*' => Http::response([
            'message' => 'Server error',
        ], 500),
    ]);

    $client = app(Client::class);
    $fetchAllPages = new FetchAllPages($client);
    $action = new ListDatabaseUsers($client, $fetchAllPages);

    $action->handle(
        serverId: 123
    );
})->throws(RequestException::class);
