<?php

use Illuminate\Http\Client\Request;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Http;
use SebastianSulinski\LaravelForgeSdk\Actions\GetDomainCertificate;
use SebastianSulinski\LaravelForgeSdk\Client;
use SebastianSulinski\LaravelForgeSdk\Exceptions\RequestFailed;

beforeEach(function () {
    config()->set('forge.token', 'test-token');
    config()->set('forge.timeout', 90);
    config()->set('forge.organisation', 'test-org');
});

it('gets domain certificate', function () {
    Http::fake([
        'forge.laravel.com/api/orgs/test-org/servers/123/sites/456/domains/789/certificate' => Http::response([
            'data' => [
                'id' => 999,
                'attributes' => [
                    'type' => 'letsencrypt',
                    'request_status' => 'created',
                    'status' => 'installed',
                    'verification_method' => 'http-01',
                    'key_type' => 'rsa',
                    'preferred_chain' => 'ISRG Root X1',
                    'created_at' => '2024-01-15T10:30:00.000000Z',
                    'updated_at' => '2024-01-15T10:30:00.000000Z',
                ],
            ],
        ]),
    ]);

    $client = app(Client::class);
    $action = new GetDomainCertificate($client);

    $certificate = $action->handle(
        serverId: 123,
        siteId: 456,
        domainRecordId: 789
    );

    expect($certificate->id)->toBe(999)
        ->and($certificate->type->value)->toBe('letsencrypt')
        ->and($certificate->requestStatus->value)->toBe('created')
        ->and($certificate->status->value)->toBe('installed')
        ->and($certificate->verificationMethod->value)->toBe('http-01')
        ->and($certificate->keyType->value)->toBe('rsa')
        ->and($certificate->preferredChain)->toBe('ISRG Root X1');

    Http::assertSent(function (Request $request) {
        return $request->url() === 'https://forge.laravel.com/api/orgs/test-org/servers/123/sites/456/domains/789/certificate'
            && $request->method() === 'GET'
            && $request->hasHeader('Authorization', 'Bearer test-token')
            && $request->hasHeader('Accept', 'application/json')
            && $request->hasHeader('Content-Type', 'application/json');
    });
});

it('throws RequestFailed exception when response data is empty', function () {
    Http::fake([
        'forge.laravel.com/api/orgs/test-org/servers/123/sites/456/domains/789/certificate' => Http::response([
            'data' => [],
        ]),
    ]);

    $client = app(Client::class);
    $action = new GetDomainCertificate($client);

    $action->handle(
        serverId: 123,
        siteId: 456,
        domainRecordId: 789
    );
})->throws(RequestFailed::class, 'Unable to get domain certificate.');

it('throws exception when request fails', function () {
    Http::fake([
        'forge.laravel.com/api/orgs/test-org/servers/123/sites/456/domains/789/certificate' => Http::response([
            'message' => 'Server error',
        ], 500),
    ]);

    $client = app(Client::class);
    $action = new GetDomainCertificate($client);

    $action->handle(
        serverId: 123,
        siteId: 456,
        domainRecordId: 789
    );
})->throws(RequestException::class);
