<?php

use Illuminate\Http\Client\Request;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Http;
use SebastianSulinski\LaravelForgeSdk\Actions\CreateDomainCertificate;
use SebastianSulinski\LaravelForgeSdk\Client;
use SebastianSulinski\LaravelForgeSdk\Enums\CertificateKeyType;
use SebastianSulinski\LaravelForgeSdk\Enums\CertificateVerificationMethod;
use SebastianSulinski\LaravelForgeSdk\Payload\CreateLetsEncryptCertificatePayload;

beforeEach(function () {
    config()->set('forge.token', 'test-token');
    config()->set('forge.timeout', 90);
    config()->set('forge.organisation', 'test-org');
});

it('creates a lets encrypt certificate', function () {
    Http::fake([
        'forge.laravel.com/api/orgs/test-org/servers/123/sites/456/domains/789/certificate' => Http::response([
            'data' => [
                'id' => 999,
                'attributes' => [
                    'type' => 'letsencrypt',
                    'request_status' => 'creating',
                    'status' => 'installing',
                    'verification_method' => 'http-01',
                    'key_type' => 'rsa',
                    'preferred_chain' => 'ISRG Root X1',
                    'created_at' => '2024-01-15T10:30:00.000000Z',
                    'updated_at' => '2024-01-15T10:30:00.000000Z',
                ],
            ],
        ], 201),
    ]);

    $client = app(Client::class);
    $action = new CreateDomainCertificate($client);

    $payload = new CreateLetsEncryptCertificatePayload(
        verification_method: CertificateVerificationMethod::Http01,
        key_type: CertificateKeyType::Rsa,
        preferred_chain: 'ISRG Root X1'
    );

    $certificate = $action->handle(
        serverId: 123,
        siteId: 456,
        domainRecordId: 789,
        payload: $payload
    );

    expect($certificate->id)->toBe(999)
        ->and($certificate->serverId)->toBe(123)
        ->and($certificate->siteId)->toBe(456)
        ->and($certificate->domainRecordId)->toBe(789)
        ->and($certificate->type->value)->toBe('letsencrypt')
        ->and($certificate->requestStatus->value)->toBe('creating')
        ->and($certificate->status->value)->toBe('installing')
        ->and($certificate->verificationMethod->value)->toBe('http-01')
        ->and($certificate->keyType->value)->toBe('rsa')
        ->and($certificate->preferredChain)->toBe('ISRG Root X1');

    Http::assertSent(function (Request $request) {
        return $request->url() === 'https://forge.laravel.com/api/orgs/test-org/servers/123/sites/456/domains/789/certificate'
            && $request->method() === 'POST'
            && $request['type'] === 'letsencrypt'
            && $request['letsencrypt']['verification_method'] === 'http-01'
            && $request['letsencrypt']['key_type'] === 'rsa'
            && $request['letsencrypt']['preferred_chain'] === 'ISRG Root X1'
            && $request->hasHeader('Authorization', 'Bearer test-token')
            && $request->hasHeader('Accept', 'application/json')
            && $request->hasHeader('Content-Type', 'application/json');
    });
});

it('creates a lets encrypt certificate with dns verification', function () {
    Http::fake([
        'forge.laravel.com/api/orgs/test-org/servers/123/sites/456/domains/789/certificate' => Http::response([
            'data' => [
                'id' => 999,
                'attributes' => [
                    'type' => 'letsencrypt',
                    'request_status' => 'verifying',
                    'status' => 'installing',
                    'verification_method' => 'dns-01',
                    'key_type' => 'ecdsa',
                    'preferred_chain' => 'ISRG Root X1',
                    'created_at' => '2024-01-15T10:30:00.000000Z',
                    'updated_at' => '2024-01-15T10:30:00.000000Z',
                ],
            ],
        ], 201),
    ]);

    $client = app(Client::class);
    $action = new CreateDomainCertificate($client);

    $payload = new CreateLetsEncryptCertificatePayload(
        verification_method: CertificateVerificationMethod::Dns01,
        key_type: CertificateKeyType::Ecdsa
    );

    $certificate = $action->handle(
        serverId: 123,
        siteId: 456,
        domainRecordId: 789,
        payload: $payload
    );

    expect($certificate->verificationMethod->value)->toBe('dns-01')
        ->and($certificate->keyType->value)->toBe('ecdsa');

    Http::assertSent(function (Request $request) {
        return $request['letsencrypt']['verification_method'] === 'dns-01'
            && $request['letsencrypt']['key_type'] === 'ecdsa';
    });
});

it('throws exception when request fails', function () {
    Http::fake([
        'forge.laravel.com/api/orgs/test-org/servers/123/sites/456/domains/789/certificate' => Http::response([
            'message' => 'Server error',
        ], 500),
    ]);

    $client = app(Client::class);
    $action = new CreateDomainCertificate($client);

    $payload = new CreateLetsEncryptCertificatePayload(
        verification_method: CertificateVerificationMethod::Http01,
        key_type: CertificateKeyType::Rsa
    );

    $action->handle(
        serverId: 123,
        siteId: 456,
        domainRecordId: 789,
        payload: $payload
    );
})->throws(RequestException::class);
