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

    /** @var \SebastianSulinski\LaravelForgeSdk\Data\Domain $first */
    $first = $domains->first();

    /** @var \SebastianSulinski\LaravelForgeSdk\Data\Domain $last */
    $last = $domains->last();

    expect($domains)->toHaveCount(2)
        ->and($first->id)->toBe(1)
        ->and($first->serverId)->toBe(123)
        ->and($first->siteId)->toBe(456)
        ->and($first->name)->toBe('example.com')
        ->and($first->type->value)->toBe('primary')
        ->and($first->status->value)->toBe('enabled')
        ->and($first->wwwRedirectType->value)->toBe('none')
        ->and($first->allowWildcardSubdomains)->toBeFalse()
        ->and($last->id)->toBe(2)
        ->and($last->name)->toBe('www.example.com')
        ->and($last->type->value)->toBe('alias')
        ->and($last->allowWildcardSubdomains)->toBeTrue();

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
