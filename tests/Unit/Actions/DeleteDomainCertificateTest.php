<?php

use Illuminate\Http\Client\Request;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Http;
use SebastianSulinski\LaravelForgeSdk\Actions\DeleteDomainCertificate;
use SebastianSulinski\LaravelForgeSdk\Client;

beforeEach(function () {
    config()->set('forge.token', 'test-token');
    config()->set('forge.timeout', 90);
    config()->set('forge.organisation', 'test-org');
});

it('deletes a domain certificate', function () {
    Http::fake([
        'forge.laravel.com/api/orgs/test-org/servers/123/sites/456/domains/789/certificate' => Http::response(null, 202),
    ]);

    $client = app(Client::class);
    $action = new DeleteDomainCertificate($client);

    $result = $action->handle(
        serverId: 123,
        siteId: 456,
        domainRecordId: 789
    );

    expect($result)->toBeTrue();

    Http::assertSent(function (Request $request) {
        return $request->url() === 'https://forge.laravel.com/api/orgs/test-org/servers/123/sites/456/domains/789/certificate'
            && $request->method() === 'DELETE'
            && $request->hasHeader('Authorization', 'Bearer test-token')
            && $request->hasHeader('Accept', 'application/json')
            && $request->hasHeader('Content-Type', 'application/json');
    });
});

it('returns false when response status is not 202', function () {
    Http::fake([
        'forge.laravel.com/api/orgs/test-org/servers/123/sites/456/domains/789/certificate' => Http::response(null, 200),
    ]);

    $client = app(Client::class);
    $action = new DeleteDomainCertificate($client);

    $result = $action->handle(
        serverId: 123,
        siteId: 456,
        domainRecordId: 789
    );

    expect($result)->toBeFalse();
});

it('throws exception when request fails with 500 error', function () {
    Http::fake([
        'forge.laravel.com/api/orgs/test-org/servers/123/sites/456/domains/789/certificate' => Http::response([
            'message' => 'Server error',
        ], 500),
    ]);

    $client = app(Client::class);
    $action = new DeleteDomainCertificate($client);

    $action->handle(
        serverId: 123,
        siteId: 456,
        domainRecordId: 789
    );
})->throws(RequestException::class);

it('throws exception when certificate is not found', function () {
    Http::fake([
        'forge.laravel.com/api/orgs/test-org/servers/123/sites/456/domains/789/certificate' => Http::response([
            'message' => 'Not found',
        ], 404),
    ]);

    $client = app(Client::class);
    $action = new DeleteDomainCertificate($client);

    $action->handle(
        serverId: 123,
        siteId: 456,
        domainRecordId: 789
    );
})->throws(RequestException::class);

it('throws exception when unauthorized', function () {
    Http::fake([
        'forge.laravel.com/api/orgs/test-org/servers/123/sites/456/domains/789/certificate' => Http::response([
            'message' => 'Unauthorized',
        ], 403),
    ]);

    $client = app(Client::class);
    $action = new DeleteDomainCertificate($client);

    $action->handle(
        serverId: 123,
        siteId: 456,
        domainRecordId: 789
    );
})->throws(RequestException::class);
