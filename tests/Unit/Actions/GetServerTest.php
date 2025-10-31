<?php

use Illuminate\Http\Client\Request;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Http;
use SebastianSulinski\LaravelForgeSdk\Actions\GetServer;
use SebastianSulinski\LaravelForgeSdk\Client;
use SebastianSulinski\LaravelForgeSdk\Exceptions\RequestFailed;

beforeEach(function () {
    config()->set('forge.token', 'test-token');
    config()->set('forge.timeout', 90);
    config()->set('forge.organisation', 'test-org');
});

it('gets server', function () {
    Http::fake([
        'forge.laravel.com/api/orgs/test-org/servers/123' => Http::response([
            'data' => [
                'id' => 123,
                'type' => 'server',
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
                    'size' => 's-2vcpu-2gb',
                    'ubuntu_version' => '22.04',
                    'php_cli_version' => 'php84',
                    'created_at' => '2024-01-15T10:30:00.000000Z',
                    'updated_at' => '2024-01-15T10:30:00.000000Z',
                ],
                'relationships' => [
                    'sites' => [
                        'links' => [
                            'related' => 'https://forge.laravel.com/api/orgs/test-org/servers/123/sites',
                        ],
                    ],
                ],
                'links' => [
                    'self' => 'https://forge.laravel.com/api/orgs/test-org/servers/123',
                ],
            ],
        ]),
    ]);

    $client = app(Client::class);
    $action = new GetServer($client);

    $server = $action->handle(serverId: 123);

    expect($server)->toBeInstanceOf(\SebastianSulinski\LaravelForgeSdk\Data\Server::class)
        ->and($server->id)->toBe(123)
        ->and($server->name)->toBe('production-server')
        ->and($server->provider)->toBe('digitalocean')
        ->and($server->region)->toBe('lon1')
        ->and($server->ipAddress)->toBe('192.168.1.1')
        ->and($server->privateIpAddress)->toBe('10.0.0.1')
        ->and($server->phpVersion)->toBe('php84')
        ->and($server->databaseType)->toBe('mysql8')
        ->and($server->connectionStatus)->toBe('connected')
        ->and($server->isReady)->toBe(true)
        ->and($server->type)->toBe('app')
        ->and($server->size)->toBe('s-2vcpu-2gb')
        ->and($server->ubuntuVersion)->toBe('22.04')
        ->and($server->phpCliVersion)->toBe('php84')
        ->and($server->relationships)->toBe([
            'sites' => [
                'links' => [
                    'related' => 'https://forge.laravel.com/api/orgs/test-org/servers/123/sites',
                ],
            ],
        ])
        ->and($server->links)->toBe([
            'self' => 'https://forge.laravel.com/api/orgs/test-org/servers/123',
        ]);

    Http::assertSent(function (Request $request) {
        return $request->url() === 'https://forge.laravel.com/api/orgs/test-org/servers/123'
            && $request->method() === 'GET'
            && $request->hasHeader('Authorization', 'Bearer test-token')
            && $request->hasHeader('Accept', 'application/json')
            && $request->hasHeader('Content-Type', 'application/json');
    });
});

it('throws RequestFailed exception when response data is empty', function () {
    Http::fake([
        'forge.laravel.com/api/orgs/test-org/servers/123' => Http::response([
            'data' => [],
        ]),
    ]);

    $client = app(Client::class);
    $action = new GetServer($client);

    $action->handle(serverId: 123);
})->throws(RequestFailed::class, 'Unable to get server.');

it('throws exception when request fails', function () {
    Http::fake([
        'forge.laravel.com/api/orgs/test-org/servers/123' => Http::response([
            'message' => 'Server error',
        ], 500),
    ]);

    $client = app(Client::class);
    $action = new GetServer($client);

    $action->handle(serverId: 123);
})->throws(RequestException::class);
