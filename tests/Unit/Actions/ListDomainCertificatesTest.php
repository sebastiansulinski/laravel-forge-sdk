<?php

use Illuminate\Http\Client\Request;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Http;
use SebastianSulinski\LaravelForgeSdk\Actions\ListDomainCertificates;
use SebastianSulinski\LaravelForgeSdk\Client;
use SebastianSulinski\LaravelForgeSdk\Data\Certificate;

beforeEach(function () {
    config()->set('forge.token', 'test-token');
    config()->set('forge.timeout', 90);
    config()->set('forge.organisation', 'test-org');
});

it('lists domain certificates', function () {
    Http::fake([
        'forge.laravel.com/api/orgs/test-org/servers/123/sites/456/domains/789/certificates' => Http::response([
            'data' => [
                [
                    'id' => 999,
                    'attributes' => [
                        'type' => 'letsencrypt',
                        'request_status' => 'created',
                        'status' => 'installed',
                        'verification_method' => 'http-01',
                        'key_type' => 'rsa',
                        'preferred_chain' => 'ISRG Root X1',
                        'active' => true,
                        'created_at' => '2024-01-15T10:30:00.000000Z',
                        'updated_at' => '2024-01-15T10:30:00.000000Z',
                    ],
                    'links' => [
                        'self' => [
                            'href' => 'https://forge.laravel.com/api/orgs/test-org/servers/123/sites/456/domains/789/certificates/999',
                        ],
                    ],
                ],
                [
                    'id' => 1000,
                    'attributes' => [
                        'type' => 'letsencrypt',
                        'request_status' => 'created',
                        'status' => 'installed',
                        'verification_method' => 'dns-01',
                        'key_type' => 'ecdsa',
                        'preferred_chain' => 'ISRG Root X1',
                        'active' => false,
                        'created_at' => '2024-01-16T10:30:00.000000Z',
                        'updated_at' => '2024-01-16T10:30:00.000000Z',
                    ],
                    'links' => [
                        'self' => [
                            'href' => 'https://forge.laravel.com/api/orgs/test-org/servers/123/sites/456/domains/789/certificates/1000',
                        ],
                    ],
                ],
            ],
        ]),
    ]);

    $client = app(Client::class);
    $action = new ListDomainCertificates($client);

    $certificates = $action->handle(
        serverId: 123,
        siteId: 456,
        domainRecordId: 789
    );

    $collection = $certificates->collection();

    /** @var Certificate $first */
    $first = $collection->first();

    /** @var Certificate $last */
    $last = $collection->last();

    expect($collection)->toHaveCount(2)
        ->and($first->id)->toBe(999)
        ->and($first->type->value)->toBe('letsencrypt')
        ->and($first->status->value)->toBe('installed')
        ->and($first->active)->toBeTrue()
        ->and($first->links('self.href'))->toBe('https://forge.laravel.com/api/orgs/test-org/servers/123/sites/456/domains/789/certificates/999')
        ->and($last->id)->toBe(1000)
        ->and($last->keyType->value)->toBe('ecdsa')
        ->and($last->active)->toBeFalse()
        ->and($last->links('self.href'))->toBe('https://forge.laravel.com/api/orgs/test-org/servers/123/sites/456/domains/789/certificates/1000');

    Http::assertSent(function (Request $request) {
        return $request->url() === 'https://forge.laravel.com/api/orgs/test-org/servers/123/sites/456/domains/789/certificates'
            && $request->method() === 'GET'
            && $request->hasHeader('Authorization', 'Bearer test-token')
            && $request->hasHeader('Accept', 'application/json')
            && $request->hasHeader('Content-Type', 'application/json');
    });
});

it('returns empty collection when no certificates found', function () {
    Http::fake([
        'forge.laravel.com/api/orgs/test-org/servers/123/sites/456/domains/789/certificates' => Http::response([
            'data' => [],
        ]),
    ]);

    $client = app(Client::class);
    $action = new ListDomainCertificates($client);

    $certificates = $action->handle(
        serverId: 123,
        siteId: 456,
        domainRecordId: 789
    );

    expect($certificates->hasData())->toBeFalse();
});

it('throws exception when request fails', function () {
    Http::fake([
        'forge.laravel.com/api/orgs/test-org/servers/123/sites/456/domains/789/certificates' => Http::response([
            'message' => 'Server error',
        ], 500),
    ]);

    $client = app(Client::class);
    $action = new ListDomainCertificates($client);

    $action->handle(
        serverId: 123,
        siteId: 456,
        domainRecordId: 789
    );
})->throws(RequestException::class);
