<?php

use Illuminate\Http\Client\Request;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Http;
use SebastianSulinski\LaravelForgeSdk\Actions\GetNginxConfiguration;
use SebastianSulinski\LaravelForgeSdk\Client;

beforeEach(function () {
    config()->set('forge.token', 'test-token');
    config()->set('forge.timeout', 90);
    config()->set('forge.organisation', 'test-org');
});

it('gets nginx configuration', function () {
    Http::fake([
        'forge.laravel.com/api/orgs/test-org/servers/123/sites/456/nginx' => Http::response([
            'data' => [
                'id' => 'nginx-config-123-456',
                'type' => 'nginxConfigs',
                'attributes' => [
                    'content' => 'server {
                        listen 80;
                        server_name example.com;
                        root /home/forge/example.com/public;
                    
                        location / {
                            try_files $uri $uri/ /index.php?$query_string;
                        }
                    }',
                ],
            ],
        ]),
    ]);

    $client = app(Client::class);
    $action = new GetNginxConfiguration($client);

    $nginxConfiguration = $action->handle(
        serverId: 123,
        siteId: 456
    );

    expect($nginxConfiguration->id)->toBe('nginx-config-123-456')
        ->and($nginxConfiguration->content)->toContain('server {')
        ->and($nginxConfiguration->content)->toContain('listen 80;')
        ->and($nginxConfiguration->content)->toContain('server_name example.com;');

    Http::assertSent(function (Request $request) {
        return $request->url() === 'https://forge.laravel.com/api/orgs/test-org/servers/123/sites/456/nginx'
            && $request->method() === 'GET'
            && $request->hasHeader('Authorization', 'Bearer test-token')
            && $request->hasHeader('Accept', 'application/json')
            && $request->hasHeader('Content-Type', 'application/json');
    });
});

it('throws exception when site not found', function () {
    Http::fake([
        'forge.laravel.com/api/orgs/test-org/servers/123/sites/456/nginx' => Http::response([
            'message' => 'Site not found',
        ], 404),
    ]);

    $client = app(Client::class);
    $action = new GetNginxConfiguration($client);

    $action->handle(
        serverId: 123,
        siteId: 456
    );
})->throws(RequestException::class);

it('throws exception when request fails', function () {
    Http::fake([
        'forge.laravel.com/api/orgs/test-org/servers/123/sites/456/nginx' => Http::response([
            'message' => 'Server error',
        ], 500),
    ]);

    $client = app(Client::class);
    $action = new GetNginxConfiguration($client);

    $action->handle(
        serverId: 123,
        siteId: 456
    );
})->throws(RequestException::class);
