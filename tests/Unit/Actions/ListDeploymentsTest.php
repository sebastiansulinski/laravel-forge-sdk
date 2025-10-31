<?php

use Illuminate\Http\Client\Request;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Http;
use SebastianSulinski\LaravelForgeSdk\Actions\FetchAllPages;
use SebastianSulinski\LaravelForgeSdk\Actions\ListDeployments;
use SebastianSulinski\LaravelForgeSdk\Client;
use SebastianSulinski\LaravelForgeSdk\Payload\ListDeploymentsPayload;

beforeEach(function () {
    config()->set('forge.token', 'test-token');
    config()->set('forge.timeout', 90);
    config()->set('forge.organisation', 'test-org');
});

it('lists deployments', function () {
    Http::fake([
        'forge.laravel.com/api/orgs/test-org/servers/123/sites/456/deployments*' => Http::response([
            'data' => [
                [
                    'id' => 1,
                    'attributes' => [
                        'type' => 'git',
                        'status' => 'finished',
                        'displayable_type' => 'App\\Models\\User',
                        'started_at' => '2024-01-15T10:30:00.000000Z',
                        'ended_at' => '2024-01-15T10:35:00.000000Z',
                        'commit' => [
                            'hash' => 'abc123',
                            'message' => 'Fix bug',
                            'author' => 'John Doe',
                            'avatar' => 'https://example.com/avatar.jpg',
                        ],
                        'created_at' => '2024-01-15T10:30:00.000000Z',
                        'updated_at' => '2024-01-15T10:35:00.000000Z',
                    ],
                ],
                [
                    'id' => 2,
                    'attributes' => [
                        'type' => 'git',
                        'status' => 'deploying',
                        'displayable_type' => 'App\\Models\\User',
                        'started_at' => '2024-01-16T10:30:00.000000Z',
                        'ended_at' => null,
                        'commit' => [
                            'hash' => 'def456',
                            'message' => 'Add feature',
                            'author' => 'Jane Smith',
                            'avatar' => 'https://example.com/avatar2.jpg',
                        ],
                        'created_at' => '2024-01-16T10:30:00.000000Z',
                        'updated_at' => '2024-01-16T10:35:00.000000Z',
                    ],
                ],
            ],
            'links' => [
                'first' => 'https://forge.laravel.com/api/orgs/test-org/servers/123/sites/456/deployments?page[size]=20',
                'last' => 'https://forge.laravel.com/api/orgs/test-org/servers/123/sites/456/deployments?page[size]=20',
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
    $action = new ListDeployments($client, $fetchAllPages);

    $deployments = $action->handle(
        serverId: 123,
        siteId: 456,
        payload: new ListDeploymentsPayload(
            filterCommitAuthor: 'John',
            filterCommitMessage: 'Fix'
        )
    );

    $collection = $deployments->collection();

    /** @var \SebastianSulinski\LaravelForgeSdk\Data\Deployment $first */
    $first = $collection->first();

    /** @var \SebastianSulinski\LaravelForgeSdk\Data\Deployment $last */
    $last = $collection->last();

    expect($collection)->toHaveCount(2)
        ->and($first->id)->toBe(1)
        ->and($first->siteId)->toBe(456)
        ->and($first->status->value)->toBe('finished')
        ->and($first->commit->hash)->toBe('abc123')
        ->and($first->commit->message)->toBe('Fix bug')
        ->and($first->commit->author)->toBe('John Doe')
        ->and($last->id)->toBe(2)
        ->and($last->status->value)->toBe('deploying')
        ->and($last->commit->hash)->toBe('def456');

    Http::assertSent(function (Request $request) {
        return str_contains($request->url(),
            'https://forge.laravel.com/api/orgs/test-org/servers/123/sites/456/deployments')
            && $request->method() === 'GET'
            && $request->hasHeader('Authorization', 'Bearer test-token')
            && $request->hasHeader('Accept', 'application/json')
            && $request->hasHeader('Content-Type', 'application/json');
    });
});

it('returns empty collection when no deployments found', function () {
    Http::fake([
        'forge.laravel.com/api/orgs/test-org/servers/123/sites/456/deployments*' => Http::response([
            'data' => [],
            'links' => [
                'first' => 'https://forge.laravel.com/api/orgs/test-org/servers/123/sites/456/deployments?page[size]=20',
                'last' => 'https://forge.laravel.com/api/orgs/test-org/servers/123/sites/456/deployments?page[size]=20',
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
    $action = new ListDeployments($client, $fetchAllPages);

    $deployments = $action->handle(
        serverId: 123,
        siteId: 456
    );

    expect($deployments->hasData())->toBeFalse();
});

it('throws exception when request fails', function () {
    Http::fake([
        'forge.laravel.com/api/orgs/test-org/servers/123/sites/456/deployments*' => Http::response([
            'message' => 'Server error',
        ], 500),
    ]);

    $client = app(Client::class);
    $fetchAllPages = new FetchAllPages($client);
    $action = new ListDeployments($client, $fetchAllPages);

    $action->handle(
        serverId: 123,
        siteId: 456
    );
})->throws(RequestException::class);
