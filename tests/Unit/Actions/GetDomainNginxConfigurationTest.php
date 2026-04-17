<?php

use Illuminate\Http\Client\Request;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Http;
use SebastianSulinski\LaravelForgeSdk\Actions\GetDomainNginxConfiguration;
use SebastianSulinski\LaravelForgeSdk\Client;

beforeEach(function () {
    config()->set('forge.token', 'test-token');
    config()->set('forge.timeout', 90);
    config()->set('forge.organisation', 'test-org');
});

it('gets domain nginx configuration', function () {
    Http::fake([
        'forge.laravel.com/api/orgs/test-org/servers/123/sites/456/domains/789/nginx' => Http::response([
            'data' => [
                'id' => 'nginx-config-123-456-789',
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
    $action = new GetDomainNginxConfiguration($client);

    $nginxConfiguration = $action->handle(
        serverId: 123,
        siteId: 456,
        domainRecordId: 789
    );

    expect($nginxConfiguration->id)->toBe('nginx-config-123-456-789')
        ->and($nginxConfiguration->content)->toContain('server {')
        ->and($nginxConfiguration->content)->toContain('listen 80;')
        ->and($nginxConfiguration->content)->toContain('server_name example.com;');

    Http::assertSent(function (Request $request) {
        return $request->url() === 'https://forge.laravel.com/api/orgs/test-org/servers/123/sites/456/domains/789/nginx'
            && $request->method() === 'GET'
            && $request->hasHeader('Authorization', 'Bearer test-token')
            && $request->hasHeader('Accept', 'application/json')
            && $request->hasHeader('Content-Type', 'application/json');
    });
});

it('throws exception when domain not found', function () {
    Http::fake([
        'forge.laravel.com/api/orgs/test-org/servers/123/sites/456/domains/789/nginx' => Http::response([
            'message' => 'Domain not found',
        ], 404),
    ]);

    $client = app(Client::class);
    $action = new GetDomainNginxConfiguration($client);

    $action->handle(
        serverId: 123,
        siteId: 456,
        domainRecordId: 789
    );
})->throws(RequestException::class);

it('throws exception when request fails', function () {
    Http::fake([
        'forge.laravel.com/api/orgs/test-org/servers/123/sites/456/domains/789/nginx' => Http::response([
            'message' => 'Server error',
        ], 500),
    ]);

    $client = app(Client::class);
    $action = new GetDomainNginxConfiguration($client);

    $action->handle(
        serverId: 123,
        siteId: 456,
        domainRecordId: 789
    );
})->throws(RequestException::class);
