<?php

use Illuminate\Http\Client\Request;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Http;
use SebastianSulinski\LaravelForgeSdk\Actions\CreateDomain;
use SebastianSulinski\LaravelForgeSdk\Client;
use SebastianSulinski\LaravelForgeSdk\Enums\WwwRedirectType;
use SebastianSulinski\LaravelForgeSdk\Exceptions\RequestFailed;
use SebastianSulinski\LaravelForgeSdk\Payload\CreateDomainPayload;

beforeEach(function () {
    config()->set('forge.token', 'test-token');
    config()->set('forge.timeout', 90);
    config()->set('forge.organisation', 'test-org');
});

it('creates a domain with default values', function () {
    Http::fake([
        'forge.laravel.com/api/orgs/test-org/servers/123/sites/456/domains' => Http::response([
            'data' => [
                'id' => 789,
                'attributes' => [
                    'name' => 'example.com',
                    'type' => 'alias',
                    'status' => 'pending',
                    'www_redirect_type' => 'none',
                    'allow_wildcard_subdomains' => false,
                    'created_at' => '2024-01-15T10:30:00.000000Z',
                    'updated_at' => '2024-01-15T10:30:00.000000Z',
                ],
            ],
        ], 201),
    ]);

    $client = app(Client::class);
    $action = new CreateDomain($client);

    $payload = new CreateDomainPayload(
        name: 'example.com'
    );

    $domain = $action->handle(
        serverId: 123,
        siteId: 456,
        payload: $payload
    );

    expect($domain->id)->toBe(789)
        ->and($domain->serverId)->toBe(123)
        ->and($domain->siteId)->toBe(456)
        ->and($domain->name)->toBe('example.com')
        ->and($domain->type->value)->toBe('alias')
        ->and($domain->status->value)->toBe('pending')
        ->and($domain->wwwRedirectType->value)->toBe('none')
        ->and($domain->allowWildcardSubdomains)->toBeFalse();

    Http::assertSent(function (Request $request) {
        return $request->url() === 'https://forge.laravel.com/api/orgs/test-org/servers/123/sites/456/domains'
            && $request->method() === 'POST'
            && $request['name'] === 'example.com'
            && $request['allow_wildcard_subdomains'] === false
            && $request['www_redirect_type'] === 'none'
            && $request->hasHeader('Authorization', 'Bearer test-token')
            && $request->hasHeader('Accept', 'application/json')
            && $request->hasHeader('Content-Type', 'application/json');
    });
});

it('creates a domain with custom values', function () {
    Http::fake([
        'forge.laravel.com/api/orgs/test-org/servers/123/sites/456/domains' => Http::response([
            'data' => [
                'id' => 789,
                'attributes' => [
                    'name' => '*.example.com',
                    'type' => 'alias',
                    'status' => 'pending',
                    'www_redirect_type' => 'to-www',
                    'allow_wildcard_subdomains' => true,
                    'created_at' => '2024-01-15T10:30:00.000000Z',
                    'updated_at' => '2024-01-15T10:30:00.000000Z',
                ],
            ],
        ], 201),
    ]);

    $client = app(Client::class);
    $action = new CreateDomain($client);

    $payload = new CreateDomainPayload(
        name: '*.example.com',
        allow_wildcard_subdomains: true,
        www_redirect_type: WwwRedirectType::ToWww
    );

    $domain = $action->handle(
        serverId: 123,
        siteId: 456,
        payload: $payload
    );

    expect($domain->name)->toBe('*.example.com')
        ->and($domain->wwwRedirectType->value)->toBe('to-www')
        ->and($domain->allowWildcardSubdomains)->toBeTrue();

    Http::assertSent(function (Request $request) {
        return $request->url() === 'https://forge.laravel.com/api/orgs/test-org/servers/123/sites/456/domains'
            && $request['name'] === '*.example.com'
            && $request['allow_wildcard_subdomains'] === true
            && $request['www_redirect_type'] === 'to-www';
    });
});

it('throws RequestFailed exception when response data is empty', function () {
    Http::fake([
        'forge.laravel.com/api/orgs/test-org/servers/123/sites/456/domains' => Http::response([
            'data' => [],
        ]),
    ]);

    $client = app(Client::class);
    $action = new CreateDomain($client);

    $payload = new CreateDomainPayload(
        name: 'example.com'
    );

    $action->handle(
        serverId: 123,
        siteId: 456,
        payload: $payload
    );
})->throws(RequestFailed::class, 'Unable to create domain.');

it('throws exception when request fails', function () {
    Http::fake([
        'forge.laravel.com/api/orgs/test-org/servers/123/sites/456/domains' => Http::response([
            'message' => 'Server error',
        ], 500),
    ]);

    $client = app(Client::class);
    $action = new CreateDomain($client);

    $payload = new CreateDomainPayload(
        name: 'example.com'
    );

    $action->handle(
        serverId: 123,
        siteId: 456,
        payload: $payload
    );
})->throws(RequestException::class);
