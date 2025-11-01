<?php

use Illuminate\Http\Client\Request;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Http;
use SebastianSulinski\LaravelForgeSdk\Actions\FetchAllPages;
use SebastianSulinski\LaravelForgeSdk\Actions\ListSites;
use SebastianSulinski\LaravelForgeSdk\Client;
use SebastianSulinski\LaravelForgeSdk\Payload\Site\ListPayload;

beforeEach(function () {
    config()->set('forge.token', 'test-token');
    config()->set('forge.timeout', 90);
    config()->set('forge.organisation', 'test-org');
});

it('lists sites', function () {
    Http::fake([
        'forge.laravel.com/api/orgs/test-org/servers/123/sites*' => Http::response([
            'data' => [
                [
                    'id' => 456,
                    'attributes' => [
                        'name' => 'example.com',
                        'status' => 'installed',
                        'url' => 'https://example.com',
                        'user' => 'forge',
                        'https' => true,
                        'web_directory' => '/public',
                        'root_directory' => '/home/forge/example.com',
                        'aliases' => [],
                        'php_version' => 'php84',
                        'deployment_status' => null,
                        'isolated' => false,
                        'shared_paths' => [],
                        'zero_downtime_deployments' => false,
                        'app_type' => 'php',
                        'uses_envoyer' => false,
                        'deployment_url' => '',
                        'repository' => [
                            'provider' => 'github',
                            'url' => 'https://github.com/user/repo',
                            'branch' => 'main',
                            'status' => 'installed',
                        ],
                        'maintenance_mode' => [
                            'enabled' => false,
                            'status' => null,
                        ],
                        'quick_deploy' => false,
                        'deployment_script' => '',
                        'wildcards' => false,
                        'healthcheck_url' => null,
                        'created_at' => '2024-01-15T10:30:00.000000Z',
                        'updated_at' => '2024-01-15T10:30:00.000000Z',
                    ],
                    'relationships' => [
                        'server' => [
                            'data' => [
                                'id' => 123,
                                'type' => 'server',
                            ],
                        ],
                    ],
                    'links' => [
                        'self' => [
                            'href' => 'https://forge.laravel.com/api/orgs/test-org/sites/456',
                        ],
                    ],
                ],
                [
                    'id' => 789,
                    'attributes' => [
                        'name' => 'test.com',
                        'status' => 'installed',
                        'url' => 'https://test.com',
                        'user' => 'forge',
                        'https' => true,
                        'web_directory' => '/public',
                        'root_directory' => '/home/forge/test.com',
                        'aliases' => [],
                        'php_version' => 'php84',
                        'deployment_status' => null,
                        'isolated' => false,
                        'shared_paths' => [],
                        'zero_downtime_deployments' => true,
                        'app_type' => 'php',
                        'uses_envoyer' => false,
                        'deployment_url' => '',
                        'repository' => [
                            'provider' => 'github',
                            'url' => 'https://github.com/user/test',
                            'branch' => 'production',
                            'status' => 'installed',
                        ],
                        'maintenance_mode' => [
                            'enabled' => false,
                            'status' => null,
                        ],
                        'quick_deploy' => true,
                        'deployment_script' => '',
                        'wildcards' => false,
                        'healthcheck_url' => null,
                        'created_at' => '2024-01-16T10:30:00.000000Z',
                        'updated_at' => '2024-01-16T10:30:00.000000Z',
                    ],
                ],
            ],
            'links' => [
                'first' => 'https://forge.laravel.com/api/orgs/test-org/servers/123/sites?page[size]=20',
                'last' => 'https://forge.laravel.com/api/orgs/test-org/servers/123/sites?page[size]=20',
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
    $action = new ListSites($client, $fetchAllPages);

    $sites = $action->handle(
        serverId: 123,
        payload: new ListPayload(
            filterName: 'example'
        )
    );

    $collection = $sites->collection();

    /** @var \SebastianSulinski\LaravelForgeSdk\Data\Site $first */
    $first = $collection->first();

    /** @var \SebastianSulinski\LaravelForgeSdk\Data\Site $last */
    $last = $collection->last();

    expect($collection)->toHaveCount(2)
        ->and($first->id)->toBe(456)
        ->and($first->name)->toBe('example.com')
        ->and($first->status->value)->toBe('installed')
        ->and($first->url)->toBe('https://example.com')
        ->and($first->webDirectory)->toBe('/public')
        ->and($first->phpVersion)->toBe('php84')
        ->and($first->repository->provider)->toBe('github')
        ->and($first->repository->branch)->toBe('main')
        ->and($last->id)->toBe(789)
        ->and($last->name)->toBe('test.com')
        ->and($last->zeroDowntimeDeployments)->toBe(true)
        ->and($last->quickDeploy)->toBe(true);

    // Test relationships and links on first site
    expect($first->relationships('server.data.id'))->toBe(123)
        ->and($first->relationships('server.data.type'))->toBe('server')
        ->and($first->links('self.href'))->toBe('https://forge.laravel.com/api/orgs/test-org/sites/456')
        ->and($first->hasRelationship('server'))->toBeTrue()
        ->and($first->hasLink('self'))->toBeTrue();

    Http::assertSent(function (Request $request) {
        return str_contains($request->url(), 'https://forge.laravel.com/api/orgs/test-org/servers/123/sites')
            && $request->method() === 'GET'
            && $request->hasHeader('Authorization', 'Bearer test-token')
            && $request->hasHeader('Accept', 'application/json')
            && $request->hasHeader('Content-Type', 'application/json');
    });
});

it('returns empty collection when no sites found', function () {
    Http::fake([
        'forge.laravel.com/api/orgs/test-org/servers/123/sites*' => Http::response([
            'data' => [],
            'links' => [
                'first' => 'https://forge.laravel.com/api/orgs/test-org/servers/123/sites?page[size]=20',
                'last' => 'https://forge.laravel.com/api/orgs/test-org/servers/123/sites?page[size]=20',
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
    $action = new ListSites($client, $fetchAllPages);

    $sites = $action->handle(serverId: 123);

    expect($sites->hasData())->toBeFalse();
});

it('throws exception when request fails', function () {
    Http::fake([
        'forge.laravel.com/api/orgs/test-org/servers/123/sites*' => Http::response([
            'message' => 'Server error',
        ], 500),
    ]);

    $client = app(Client::class);
    $fetchAllPages = new FetchAllPages($client);
    $action = new ListSites($client, $fetchAllPages);

    $action->handle(serverId: 123);
})->throws(RequestException::class);
