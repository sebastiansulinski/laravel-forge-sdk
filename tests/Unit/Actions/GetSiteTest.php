<?php

use Illuminate\Http\Client\Request;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Http;
use SebastianSulinski\LaravelForgeSdk\Actions\GetSite;
use SebastianSulinski\LaravelForgeSdk\Client;

beforeEach(function () {
    config()->set('forge.token', 'test-token');
    config()->set('forge.timeout', 90);
    config()->set('forge.organisation', 'test-org');
});

it('gets site', function () {
    Http::fake([
        'forge.laravel.com/api/orgs/test-org/sites/456' => Http::response([
            'data' => [
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
                    'php_version' => 'php83',
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
                        'links' => [
                            'related' => 'https://forge.laravel.com/api/orgs/test-org/servers/123',
                        ],
                    ],
                    'tags' => [
                        'data' => [
                            ['id' => 1, 'type' => 'tag'],
                            ['id' => 2, 'type' => 'tag'],
                        ],
                    ],
                ],
                'links' => [
                    'self' => [
                        'href' => 'https://forge.laravel.com/api/orgs/test-org/sites/456',
                    ],
                ],
            ],
        ]),
    ]);

    $client = app(Client::class);
    $action = new GetSite($client);

    $site = $action->handle(
        siteId: 456
    );

    expect($site->id)->toBe(456)
        ->and($site->name)->toBe('example.com')
        ->and($site->status->value)->toBe('installed')
        ->and($site->url)->toBe('https://example.com')
        ->and($site->webDirectory)->toBe('/public')
        ->and($site->phpVersion)->toBe('php83')
        ->and($site->repository->provider)->toBe('github')
        ->and($site->repository->branch)->toBe('main');

    // Test relationships and links direct access
    expect($site->relationships)->toBeArray()
        ->and($site->links)->toBeArray();

    // Test relationship accessor methods
    expect($site->relationships('server.data.id'))->toBe(123)
        ->and($site->relationships('server.data.type'))->toBe('server')
        ->and($site->relationships('server.links.related'))->toBe('https://forge.laravel.com/api/orgs/test-org/servers/123')
        ->and($site->relationships('tags.data.0.id'))->toBe(1)
        ->and($site->relationships('tags.data.1.type'))->toBe('tag');

    // Test link accessor methods
    expect($site->links('self.href'))->toBe('https://forge.laravel.com/api/orgs/test-org/sites/456');

    // Test with default values
    expect($site->relationships('nonexistent.key', 'default'))->toBe('default')
        ->and($site->links('nonexistent.key', 'default'))->toBe('default');

    // Test existence checkers
    expect($site->hasRelationship('server'))->toBeTrue()
        ->and($site->hasRelationship('nonexistent'))->toBeFalse()
        ->and($site->hasLink('self'))->toBeTrue()
        ->and($site->hasLink('nonexistent'))->toBeFalse();

    Http::assertSent(function (Request $request) {
        return $request->url() === 'https://forge.laravel.com/api/orgs/test-org/sites/456'
            && $request->method() === 'GET'
            && $request->hasHeader('Authorization', 'Bearer test-token')
            && $request->hasHeader('Accept', 'application/json')
            && $request->hasHeader('Content-Type', 'application/json');
    });
});

it('throws exception when site not found', function () {
    Http::fake([
        'forge.laravel.com/api/orgs/test-org/sites/456' => Http::response([
            'message' => 'Site not found',
        ], 404),
    ]);

    $client = app(Client::class);
    $action = new GetSite($client);

    $action->handle(
        siteId: 456
    );
})->throws(RequestException::class);

it('throws exception when request fails', function () {
    Http::fake([
        'forge.laravel.com/api/orgs/test-org/sites/456' => Http::response([
            'message' => 'Server error',
        ], 500),
    ]);

    $client = app(Client::class);
    $action = new GetSite($client);

    $action->handle(
        siteId: 456
    );
})->throws(RequestException::class);
