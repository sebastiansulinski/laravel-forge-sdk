<?php

use Illuminate\Http\Client\Request;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Http;
use SebastianSulinski\LaravelForgeSdk\Actions\ListDomains;
use SebastianSulinski\LaravelForgeSdk\Client;

beforeEach(function () {
    config()->set('forge.token', 'test-token');
    config()->set('forge.timeout', 90);
    config()->set('forge.organisation', 'test-org');
});

it('lists domains', function () {
    Http::fake([
        'forge.laravel.com/api/orgs/test-org/servers/123/sites/456/domains' => Http::response([
            'data' => [
                [
                    'id' => 1,
                    'attributes' => [
                        'name' => 'example.com',
                        'type' => 'primary',
                        'status' => 'enabled',
                        'www_redirect_type' => 'none',
                        'allow_wildcard_subdomains' => false,
                        'created_at' => '2024-01-15T10:30:00.000000Z',
                        'updated_at' => '2024-01-15T10:30:00.000000Z',
                    ],
                ],
                [
                    'id' => 2,
                    'attributes' => [
                        'name' => 'www.example.com',
                        'type' => 'alias',
                        'status' => 'enabled',
                        'www_redirect_type' => 'from-www',
                        'allow_wildcard_subdomains' => true,
                        'created_at' => '2024-01-16T10:30:00.000000Z',
                        'updated_at' => '2024-01-16T10:30:00.000000Z',
                    ],
                ],
            ],
        ]),
    ]);

    $client = app(Client::class);
    $action = new ListDomains($client);

    $domains = $action->handle(
        serverId: 123,
        siteId: 456
    );

    expect($domains)->toHaveCount(2)
        ->and($domains->first()->id)->toBe(1)
        ->and($domains->first()->serverId)->toBe(123)
        ->and($domains->first()->siteId)->toBe(456)
        ->and($domains->first()->name)->toBe('example.com')
        ->and($domains->first()->type->value)->toBe('primary')
        ->and($domains->first()->status->value)->toBe('enabled')
        ->and($domains->first()->wwwRedirectType->value)->toBe('none')
        ->and($domains->first()->allowWildcardSubdomains)->toBe(false)
        ->and($domains->last()->id)->toBe(2)
        ->and($domains->last()->name)->toBe('www.example.com')
        ->and($domains->last()->type->value)->toBe('alias')
        ->and($domains->last()->allowWildcardSubdomains)->toBe(true);

    Http::assertSent(function (Request $request) {
        return $request->url() === 'https://forge.laravel.com/api/orgs/test-org/servers/123/sites/456/domains'
            && $request->method() === 'GET'
            && $request->hasHeader('Authorization', 'Bearer test-token')
            && $request->hasHeader('Accept', 'application/json')
            && $request->hasHeader('Content-Type', 'application/json');
    });
});

it('returns empty collection when no domains found', function () {
    Http::fake([
        'forge.laravel.com/api/orgs/test-org/servers/123/sites/456/domains' => Http::response([
            'data' => [],
        ]),
    ]);

    $client = app(Client::class);
    $action = new ListDomains($client);

    $domains = $action->handle(
        serverId: 123,
        siteId: 456
    );

    expect($domains)->toBeEmpty();
});

it('throws exception when request fails', function () {
    Http::fake([
        'forge.laravel.com/api/orgs/test-org/servers/123/sites/456/domains' => Http::response([
            'message' => 'Server error',
        ], 500),
    ]);

    $client = app(Client::class);
    $action = new ListDomains($client);

    $action->handle(
        serverId: 123,
        siteId: 456
    );
})->throws(RequestException::class);
