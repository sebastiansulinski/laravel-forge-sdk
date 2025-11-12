<?php

use Illuminate\Http\Client\Request;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Http;
use SebastianSulinski\LaravelForgeSdk\Actions\CreateDomainCertificateAction;
use SebastianSulinski\LaravelForgeSdk\Client;
use SebastianSulinski\LaravelForgeSdk\Enums\Certificate\Action;
use SebastianSulinski\LaravelForgeSdk\Payload\Certificate\CreateActionPayload;

beforeEach(function () {
    config()->set('forge.token', 'test-token');
    config()->set('forge.timeout', 90);
    config()->set('forge.organisation', 'test-org');
});

it('enables a domain certificate', function () {
    Http::fake([
        'forge.laravel.com/api/orgs/test-org/servers/123/sites/456/domains/789/certificate/actions' => Http::response(null, 200),
    ]);

    $client = app(Client::class);
    $action = new CreateDomainCertificateAction($client);
    $payload = new CreateActionPayload(action: Action::Enable);

    $result = $action->handle(
        serverId: 123,
        siteId: 456,
        domainRecordId: 789,
        payload: $payload
    );

    expect($result)->toBeTrue();

    Http::assertSent(function (Request $request) {
        return $request->url() === 'https://forge.laravel.com/api/orgs/test-org/servers/123/sites/456/domains/789/certificate/actions'
            && $request->method() === 'POST'
            && $request->hasHeader('Authorization', 'Bearer test-token')
            && $request->hasHeader('Accept', 'application/json')
            && $request->hasHeader('Content-Type', 'application/json')
            && $request->data() === ['action' => 'enable'];
    });
});

it('disables a domain certificate', function () {
    Http::fake([
        'forge.laravel.com/api/orgs/test-org/servers/123/sites/456/domains/789/certificate/actions' => Http::response(null, 200),
    ]);

    $client = app(Client::class);
    $action = new CreateDomainCertificateAction($client);
    $payload = new CreateActionPayload(action: Action::Disable);

    $result = $action->handle(
        serverId: 123,
        siteId: 456,
        domainRecordId: 789,
        payload: $payload
    );

    expect($result)->toBeTrue();

    Http::assertSent(function (Request $request) {
        return $request->url() === 'https://forge.laravel.com/api/orgs/test-org/servers/123/sites/456/domains/789/certificate/actions'
            && $request->method() === 'POST'
            && $request->data() === ['action' => 'disable'];
    });
});

it('returns true when response is successful with 201 status', function () {
    Http::fake([
        'forge.laravel.com/api/orgs/test-org/servers/123/sites/456/domains/789/certificate/actions' => Http::response(null, 201),
    ]);

    $client = app(Client::class);
    $action = new CreateDomainCertificateAction($client);
    $payload = new CreateActionPayload(action: Action::Enable);

    $result = $action->handle(
        serverId: 123,
        siteId: 456,
        domainRecordId: 789,
        payload: $payload
    );

    expect($result)->toBeTrue();
});

it('returns true when response is successful with 204 status', function () {
    Http::fake([
        'forge.laravel.com/api/orgs/test-org/servers/123/sites/456/domains/789/certificate/actions' => Http::response(null, 204),
    ]);

    $client = app(Client::class);
    $action = new CreateDomainCertificateAction($client);
    $payload = new CreateActionPayload(action: Action::Enable);

    $result = $action->handle(
        serverId: 123,
        siteId: 456,
        domainRecordId: 789,
        payload: $payload
    );

    expect($result)->toBeTrue();
});

it('throws exception when request fails with 500 error', function () {
    Http::fake([
        'forge.laravel.com/api/orgs/test-org/servers/123/sites/456/domains/789/certificate/actions' => Http::response([
            'message' => 'Server error',
        ], 500),
    ]);

    $client = app(Client::class);
    $action = new CreateDomainCertificateAction($client);
    $payload = new CreateActionPayload(action: Action::Enable);

    $action->handle(
        serverId: 123,
        siteId: 456,
        domainRecordId: 789,
        payload: $payload
    );
})->throws(RequestException::class);

it('throws exception when certificate is not found', function () {
    Http::fake([
        'forge.laravel.com/api/orgs/test-org/servers/123/sites/456/domains/789/certificate/actions' => Http::response([
            'message' => 'Not found',
        ], 404),
    ]);

    $client = app(Client::class);
    $action = new CreateDomainCertificateAction($client);
    $payload = new CreateActionPayload(action: Action::Enable);

    $action->handle(
        serverId: 123,
        siteId: 456,
        domainRecordId: 789,
        payload: $payload
    );
})->throws(RequestException::class);

it('throws exception when unauthorized', function () {
    Http::fake([
        'forge.laravel.com/api/orgs/test-org/servers/123/sites/456/domains/789/certificate/actions' => Http::response([
            'message' => 'Unauthorized',
        ], 403),
    ]);

    $client = app(Client::class);
    $action = new CreateDomainCertificateAction($client);
    $payload = new CreateActionPayload(action: Action::Enable);

    $action->handle(
        serverId: 123,
        siteId: 456,
        domainRecordId: 789,
        payload: $payload
    );
})->throws(RequestException::class);

it('throws exception when bad request', function () {
    Http::fake([
        'forge.laravel.com/api/orgs/test-org/servers/123/sites/456/domains/789/certificate/actions' => Http::response([
            'message' => 'Invalid action',
        ], 400),
    ]);

    $client = app(Client::class);
    $action = new CreateDomainCertificateAction($client);
    $payload = new CreateActionPayload(action: Action::Enable);

    $action->handle(
        serverId: 123,
        siteId: 456,
        domainRecordId: 789,
        payload: $payload
    );
})->throws(RequestException::class);

it('throws exception when unprocessable entity', function () {
    Http::fake([
        'forge.laravel.com/api/orgs/test-org/servers/123/sites/456/domains/789/certificate/actions' => Http::response([
            'message' => 'Validation failed',
            'errors' => [
                'action' => ['The action field is required.'],
            ],
        ], 422),
    ]);

    $client = app(Client::class);
    $action = new CreateDomainCertificateAction($client);
    $payload = new CreateActionPayload(action: Action::Enable);

    $action->handle(
        serverId: 123,
        siteId: 456,
        domainRecordId: 789,
        payload: $payload
    );
})->throws(RequestException::class);
