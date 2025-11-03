<?php

use Illuminate\Http\Client\Request;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Http;
use SebastianSulinski\LaravelForgeSdk\Actions\GetDomain;
use SebastianSulinski\LaravelForgeSdk\Client;
use SebastianSulinski\LaravelForgeSdk\Exceptions\RequestFailed;

beforeEach(function () {
    config()->set('forge.token', 'test-token');
    config()->set('forge.timeout', 90);
    config()->set('forge.organisation', 'test-org');
});

it('gets domain', function () {
    Http::fake([
        'forge.laravel.com/api/orgs/test-org/servers/123/sites/456/domains/789' => Http::response([
            'data' => [
                'id' => 789,
                'attributes' => [
                    'name' => 'example.com',
                    'type' => 'primary',
                    'status' => 'enabled',
                    'www_redirect_type' => 'none',
                    'allow_wildcard_subdomains' => false,
                    'created_at' => '2024-01-15T10:30:00.000000Z',
                    'updated_at' => '2024-01-15T10:30:00.000000Z',
                ],
                'links' => [
                    'self' => 'https://forge.laravel.com/api/orgs/test-org/servers/123/sites/456/domains/789',
                ],
            ],
        ]),
    ]);

    $client = app(Client::class);
    $action = new GetDomain($client);

    $domain = $action->handle(
        serverId: 123,
        siteId: 456,
        domainRecordId: 789
    );

    expect($domain->id)->toBe(789)
        ->and($domain->name)->toBe('example.com')
        ->and($domain->type->value)->toBe('primary')
        ->and($domain->status->value)->toBe('enabled')
        ->and($domain->wwwRedirectType->value)->toBe('none')
        ->and($domain->allowWildcardSubdomains)->toBe(false)
        ->and($domain->createdAt)->not()->toBeNull()
        ->and($domain->updatedAt)->not()->toBeNull()
        ->and($domain->links)->toHaveKey('self');

    Http::assertSent(function (Request $request) {
        return $request->url() === 'https://forge.laravel.com/api/orgs/test-org/servers/123/sites/456/domains/789'
            && $request->method() === 'GET'
            && $request->hasHeader('Authorization', 'Bearer test-token')
            && $request->hasHeader('Accept', 'application/json')
            && $request->hasHeader('Content-Type', 'application/json');
    });
});

it('throws RequestFailed exception when response data is empty', function () {
    Http::fake([
        'forge.laravel.com/api/orgs/test-org/servers/123/sites/456/domains/789' => Http::response([
            'data' => [],
        ]),
    ]);

    $client = app(Client::class);
    $action = new GetDomain($client);

    $action->handle(
        serverId: 123,
        siteId: 456,
        domainRecordId: 789
    );
})->throws(RequestFailed::class, 'Unable to get domain.');

it('throws exception when request fails', function () {
    Http::fake([
        'forge.laravel.com/api/orgs/test-org/servers/123/sites/456/domains/789' => Http::response([
            'message' => 'Server error',
        ], 500),
    ]);

    $client = app(Client::class);
    $action = new GetDomain($client);

    $action->handle(
        serverId: 123,
        siteId: 456,
        domainRecordId: 789
    );
})->throws(RequestException::class);
