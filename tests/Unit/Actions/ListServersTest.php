<?php

use Illuminate\Http\Client\Request;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Http;
use SebastianSulinski\LaravelForgeSdk\Actions\FetchAllPages;
use SebastianSulinski\LaravelForgeSdk\Actions\ListServers;
use SebastianSulinski\LaravelForgeSdk\Client;
use SebastianSulinski\LaravelForgeSdk\Payload\ListServersPayload;

beforeEach(function () {
    config()->set('forge.token', 'test-token');
    config()->set('forge.timeout', 90);
    config()->set('forge.organisation', 'test-org');
});

it('lists servers', function () {
    Http::fake([
        'forge.laravel.com/api/orgs/test-org/servers*' => Http::response([
            'data' => [
                [
                    'id' => 1,
                    'attributes' => [
                        'name' => 'production-server',
                        'provider' => 'digitalocean',
                        'region' => 'lon1',
                        'ip_address' => '192.168.1.1',
                        'private_ip_address' => '10.0.0.1',
                        'php_version' => 'php84',
                        'database_type' => 'mysql8',
                        'connection_status' => 'connected',
                        'is_ready' => true,
                        'type' => 'app',
                        'size' => 's-1vcpu-1gb',
                        'ubuntu_version' => '22.04',
                        'php_cli_version' => 'php84',
                        'created_at' => '2024-01-15T10:30:00.000000Z',
                        'updated_at' => '2024-01-15T10:30:00.000000Z',
                    ],
                ],
                [
                    'id' => 2,
                    'attributes' => [
                        'name' => 'staging-server',
                        'provider' => 'digitalocean',
                        'region' => 'nyc1',
                        'ip_address' => '192.168.1.2',
                        'private_ip_address' => '10.0.0.2',
                        'php_version' => 'php84',
                        'database_type' => 'mysql8',
                        'connection_status' => 'connected',
                        'is_ready' => true,
                        'type' => 'app',
                        'size' => 's-1vcpu-1gb',
                        'ubuntu_version' => '22.04',
                        'php_cli_version' => 'php84',
                        'created_at' => '2024-01-16T10:30:00.000000Z',
                        'updated_at' => '2024-01-16T10:30:00.000000Z',
                    ],
                ],
            ],
            'links' => [
                'first' => 'https://forge.laravel.com/api/orgs/test-org/servers?page[size]=20',
                'last' => 'https://forge.laravel.com/api/orgs/test-org/servers?page[size]=20',
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
    $action = new ListServers($client, $fetchAllPages);

    $servers = $action->handle(
        payload: new ListServersPayload(
            filterProvider: 'digitalocean',
            filterRegion: 'lon1'
        )
    );

    expect($servers)->toHaveCount(2)
        ->and($servers->first()->id)->toBe(1)
        ->and($servers->first()->name)->toBe('production-server')
        ->and($servers->first()->provider)->toBe('digitalocean')
        ->and($servers->first()->region)->toBe('lon1')
        ->and($servers->first()->ipAddress)->toBe('192.168.1.1')
        ->and($servers->first()->isReady)->toBe(true)
        ->and($servers->first()->type)->toBe('app')
        ->and($servers->last()->id)->toBe(2)
        ->and($servers->last()->name)->toBe('staging-server')
        ->and($servers->last()->region)->toBe('nyc1');

    Http::assertSent(function (Request $request) {
        return str_contains($request->url(), 'https://forge.laravel.com/api/orgs/test-org/servers')
            && $request->method() === 'GET'
            && $request->hasHeader('Authorization', 'Bearer test-token')
            && $request->hasHeader('Accept', 'application/json')
            && $request->hasHeader('Content-Type', 'application/json');
    });
});

it('returns empty collection when no servers found', function () {
    Http::fake([
        'forge.laravel.com/api/orgs/test-org/servers*' => Http::response([
            'data' => [],
            'links' => [
                'first' => 'https://forge.laravel.com/api/orgs/test-org/servers?page[size]=20',
                'last' => 'https://forge.laravel.com/api/orgs/test-org/servers?page[size]=20',
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
    $action = new ListServers($client, $fetchAllPages);

    $servers = $action->handle();

    expect($servers)->toBeEmpty();
});

it('throws exception when request fails', function () {
    Http::fake([
        'forge.laravel.com/api/orgs/test-org/servers*' => Http::response([
            'message' => 'Server error',
        ], 500),
    ]);

    $client = app(Client::class);
    $fetchAllPages = new FetchAllPages($client);
    $action = new ListServers($client, $fetchAllPages);

    $action->handle();
})->throws(RequestException::class);
