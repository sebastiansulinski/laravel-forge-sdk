<?php

use Illuminate\Http\Client\Request;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Http;
use SebastianSulinski\LaravelForgeSdk\Actions\UpdateDomainNginxConfiguration;
use SebastianSulinski\LaravelForgeSdk\Client;
use SebastianSulinski\LaravelForgeSdk\Payload\Nginx\UpdatePayload;

beforeEach(function () {
    config()->set('forge.token', 'test-token');
    config()->set('forge.timeout', 90);
    config()->set('forge.organisation', 'test-org');
});

it('updates domain nginx configuration', function () {
    Http::fake([
        'forge.laravel.com/api/orgs/test-org/servers/123/sites/456/domains/789/nginx' => Http::response(null, 202),
    ]);

    $client = app(Client::class);
    $action = new UpdateDomainNginxConfiguration($client);

    $config = implode(PHP_EOL, [
        'server {',
        '    listen 80;',
        '    server_name example.com;',
        '    root /home/forge/example.com/public;',
        '',
        '    location / {',
        '        try_files $uri $uri/ /index.php?$query_string;',
        '    }',
        '}',
    ]);

    $payload = new UpdatePayload(config: $config);

    $result = $action->handle(
        serverId: 123,
        siteId: 456,
        domainRecordId: 789,
        payload: $payload
    );

    expect($result)->toBeTrue();

    Http::assertSent(function (Request $request) use ($config) {
        return $request->url() === 'https://forge.laravel.com/api/orgs/test-org/servers/123/sites/456/domains/789/nginx'
            && $request->method() === 'PUT'
            && $request['config'] === $config
            && $request->hasHeader('Authorization', 'Bearer test-token')
            && $request->hasHeader('Accept', 'application/json')
            && $request->hasHeader('Content-Type', 'application/json');
    });
});

it('throws exception when request fails', function () {
    Http::fake([
        'forge.laravel.com/api/orgs/test-org/servers/123/sites/456/domains/789/nginx' => Http::response([
            'message' => 'Server error',
        ], 500),
    ]);

    $client = app(Client::class);
    $action = new UpdateDomainNginxConfiguration($client);

    $payload = new UpdatePayload(config: 'server { }');

    $action->handle(
        serverId: 123,
        siteId: 456,
        domainRecordId: 789,
        payload: $payload
    );
})->throws(RequestException::class);
