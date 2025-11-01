<?php

use Illuminate\Http\Client\Request;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Http;
use SebastianSulinski\LaravelForgeSdk\Actions\UpdateSite;
use SebastianSulinski\LaravelForgeSdk\Client;
use SebastianSulinski\LaravelForgeSdk\Enums\Server\PhpVersion;
use SebastianSulinski\LaravelForgeSdk\Enums\Site\Type;
use SebastianSulinski\LaravelForgeSdk\Payload\Site\UpdatePayload;

beforeEach(function () {
    config()->set('forge.token', 'test-token');
    config()->set('forge.timeout', 90);
    config()->set('forge.organisation', 'test-org');
});

it('updates site', function () {
    Http::fake([
        'forge.laravel.com/api/orgs/test-org/servers/123/sites/456' => Http::response(),
    ]);

    $client = app(Client::class);
    $action = new UpdateSite($client);

    $payload = new UpdatePayload(
        directory: '/public_html',
        type: Type::Php,
        php_version: PhpVersion::Php84,
        push_to_deploy: true,
        repository_branch: 'production'
    );

    $action->handle(
        serverId: 123,
        siteId: 456,
        payload: $payload
    );

    Http::assertSent(function (Request $request) {
        return $request->url() === 'https://forge.laravel.com/api/orgs/test-org/servers/123/sites/456'
            && $request->method() === 'PUT'
            && $request['directory'] === '/public_html'
            && $request['type'] === 'php'
            && $request['php_version'] === 'php84'
            && $request['push_to_deploy'] === true
            && $request['repository_branch'] === 'production'
            && $request->hasHeader('Authorization', 'Bearer test-token')
            && $request->hasHeader('Accept', 'application/json')
            && $request->hasHeader('Content-Type', 'application/json');
    });
});

it('updates site with partial data', function () {
    Http::fake([
        'forge.laravel.com/api/orgs/test-org/servers/123/sites/456' => Http::response(),
    ]);

    $client = app(Client::class);
    $action = new UpdateSite($client);

    $payload = new UpdatePayload(
        php_version: PhpVersion::Php85,
        push_to_deploy: false
    );

    $action->handle(
        serverId: 123,
        siteId: 456,
        payload: $payload
    );

    Http::assertSent(function (Request $request) {
        return $request->url() === 'https://forge.laravel.com/api/orgs/test-org/servers/123/sites/456'
            && $request->method() === 'PUT'
            && $request['php_version'] === 'php85'
            && $request['push_to_deploy'] === false
            && ! isset($request['directory'])
            && ! isset($request['type'])
            && ! isset($request['repository_branch'])
            && $request->hasHeader('Authorization', 'Bearer test-token')
            && $request->hasHeader('Accept', 'application/json')
            && $request->hasHeader('Content-Type', 'application/json');
    });
});

it('throws exception when request fails', function () {
    Http::fake([
        'forge.laravel.com/api/orgs/test-org/servers/123/sites/456' => Http::response([
            'message' => 'Server error',
        ], 500),
    ]);

    $client = app(Client::class);
    $action = new UpdateSite($client);

    $payload = new UpdatePayload(
        directory: '/public'
    );

    $action->handle(
        serverId: 123,
        siteId: 456,
        payload: $payload
    );
})->throws(RequestException::class);
