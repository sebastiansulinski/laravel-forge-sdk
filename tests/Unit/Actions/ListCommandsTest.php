<?php

use Illuminate\Http\Client\Request;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Http;
use SebastianSulinski\LaravelForgeSdk\Actions\FetchAllPages;
use SebastianSulinski\LaravelForgeSdk\Actions\ListCommands;
use SebastianSulinski\LaravelForgeSdk\Client;
use SebastianSulinski\LaravelForgeSdk\Payload\Command\ListPayload;

beforeEach(function () {
    config()->set('forge.token', 'test-token');
    config()->set('forge.timeout', 90);
    config()->set('forge.organisation', 'test-org');
});

it('lists commands', function () {
    Http::fake([
        'forge.laravel.com/api/orgs/test-org/servers/123/sites/456/commands*' => Http::response([
            'data' => [
                [
                    'id' => 1,
                    'attributes' => [
                        'command' => 'php artisan migrate',
                        'status' => 'finished',
                        'user_id' => 100,
                        'duration' => 45,
                        'created_at' => '2024-01-15T10:30:00.000000Z',
                        'updated_at' => '2024-01-15T10:30:45.000000Z',
                    ],
                ],
                [
                    'id' => 2,
                    'attributes' => [
                        'command' => 'php artisan cache:clear',
                        'status' => 'running',
                        'user_id' => 101,
                        'duration' => null,
                        'created_at' => '2024-01-16T10:30:00.000000Z',
                        'updated_at' => '2024-01-16T10:30:05.000000Z',
                    ],
                ],
            ],
            'links' => [
                'first' => 'https://forge.laravel.com/api/orgs/test-org/servers/123/sites/456/commands?page[size]=20',
                'last' => 'https://forge.laravel.com/api/orgs/test-org/servers/123/sites/456/commands?page[size]=20',
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
    $action = new ListCommands($client, $fetchAllPages);

    $commands = $action->handle(
        serverId: 123,
        siteId: 456,
        payload: new ListPayload(
            filterUserId: 100,
            filterStatus: 'finished'
        )
    );

    $collection = $commands->collection();

    /** @var \SebastianSulinski\LaravelForgeSdk\Data\Command $first */
    $first = $collection->first();

    /** @var \SebastianSulinski\LaravelForgeSdk\Data\Command $last */
    $last = $collection->last();

    expect($collection)->toHaveCount(2)
        ->and($first->id)->toBe(1)
        ->and($first->siteId)->toBe(456)
        ->and($first->command)->toBe('php artisan migrate')
        ->and($first->status->value)->toBe('finished')
        ->and($first->userId)->toBe(100)
        ->and($first->duration)->toBe(45)
        ->and($last->id)->toBe(2)
        ->and($last->command)->toBe('php artisan cache:clear')
        ->and($last->status->value)->toBe('running')
        ->and($last->userId)->toBe(101)
        ->and($last->duration)->toBeNull();

    Http::assertSent(function (Request $request) {
        return str_contains($request->url(),
            'https://forge.laravel.com/api/orgs/test-org/servers/123/sites/456/commands')
            && $request->method() === 'GET'
            && $request->hasHeader('Authorization', 'Bearer test-token')
            && $request->hasHeader('Accept', 'application/json')
            && $request->hasHeader('Content-Type', 'application/json');
    });
});

it('returns empty collection when no commands found', function () {
    Http::fake([
        'forge.laravel.com/api/orgs/test-org/servers/123/sites/456/commands*' => Http::response([
            'data' => [],
            'links' => [
                'first' => 'https://forge.laravel.com/api/orgs/test-org/servers/123/sites/456/commands?page[size]=20',
                'last' => 'https://forge.laravel.com/api/orgs/test-org/servers/123/sites/456/commands?page[size]=20',
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
    $action = new ListCommands($client, $fetchAllPages);

    $commands = $action->handle(
        serverId: 123,
        siteId: 456
    );

    expect($commands->hasData())->toBeFalse();
});

it('throws exception when request fails', function () {
    Http::fake([
        'forge.laravel.com/api/orgs/test-org/servers/123/sites/456/commands*' => Http::response([
            'message' => 'Server error',
        ], 500),
    ]);

    $client = app(Client::class);
    $fetchAllPages = new FetchAllPages($client);
    $action = new ListCommands($client, $fetchAllPages);

    $action->handle(
        serverId: 123,
        siteId: 456
    );
})->throws(RequestException::class);
