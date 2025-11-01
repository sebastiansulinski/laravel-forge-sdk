<?php

use Carbon\Carbon;
use Illuminate\Http\Client\Request;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Http;
use SebastianSulinski\LaravelForgeSdk\Actions\GetDeploymentStatus;
use SebastianSulinski\LaravelForgeSdk\Client;
use SebastianSulinski\LaravelForgeSdk\Exceptions\RequestFailed;

beforeEach(function () {
    config()->set('forge.token', 'test-token');
    config()->set('forge.timeout', 90);
    config()->set('forge.organisation', 'test-org');
});

it('gets deployment status', function () {
    Http::fake([
        'forge.laravel.com/api/orgs/test-org/servers/123/sites/456/deployments/status' => Http::response([
            'data' => [
                'id' => 789,
                'attributes' => [
                    'status' => 'finished',
                    'started_at' => '2024-01-15T10:30:00.000000Z',
                ],
            ],
        ]),
    ]);

    $client = app(Client::class);
    $action = new GetDeploymentStatus($client);

    $deploymentStatus = $action->handle(
        serverId: 123,
        siteId: 456
    );

    expect($deploymentStatus->id)->toBe('789')
        ->and($deploymentStatus->serverId)->toBe(123)
        ->and($deploymentStatus->siteId)->toBe(456)
        ->and($deploymentStatus->status->value)->toBe('finished')
        ->and($deploymentStatus->startedAt)->toBeInstanceOf(Carbon::class);

    Http::assertSent(function (Request $request) {
        return $request->url() === 'https://forge.laravel.com/api/orgs/test-org/servers/123/sites/456/deployments/status'
            && $request->method() === 'GET'
            && $request->hasHeader('Authorization', 'Bearer test-token')
            && $request->hasHeader('Accept', 'application/json')
            && $request->hasHeader('Content-Type', 'application/json');
    });
});

it('gets deployment status with deploying state', function () {
    Http::fake([
        'forge.laravel.com/api/orgs/test-org/servers/123/sites/456/deployments/status' => Http::response([
            'data' => [
                'id' => 789,
                'attributes' => [
                    'status' => 'deploying',
                    'started_at' => '2024-01-15T10:30:00.000000Z',
                ],
            ],
        ]),
    ]);

    $client = app(Client::class);
    $action = new GetDeploymentStatus($client);

    $deploymentStatus = $action->handle(
        serverId: 123,
        siteId: 456
    );

    expect($deploymentStatus->status->value)->toBe('deploying');
});

it('sets status to null when response status is null', function () {
    Http::fake([
        'forge.laravel.com/api/orgs/test-org/servers/123/sites/456/deployments/status' => Http::response([
            'data' => [
                'id' => 789,
                'attributes' => [
                    'status' => null,
                    'started_at' => '2024-01-15T10:30:00.000000Z',
                ],
            ],
        ]),
    ]);

    $client = app(Client::class);
    $action = new GetDeploymentStatus($client);

    $deploymentStatus = $action->handle(
        serverId: 123,
        siteId: 456
    );

    expect($deploymentStatus->status)->toBeNull();
});

it('throws RequestFailed exception when response data is empty', function () {
    Http::fake([
        'forge.laravel.com/api/orgs/test-org/servers/123/sites/456/deployments/status' => Http::response([
            'data' => [],
        ]),
    ]);

    $client = app(Client::class);
    $action = new GetDeploymentStatus($client);

    $action->handle(
        serverId: 123,
        siteId: 456
    );
})->throws(RequestFailed::class, 'Unable to get deployment status.');

it('throws exception when request fails', function () {
    Http::fake([
        'forge.laravel.com/api/orgs/test-org/servers/123/sites/456/deployments/status' => Http::response([
            'message' => 'Server error',
        ], 500),
    ]);

    $client = app(Client::class);
    $action = new GetDeploymentStatus($client);

    $action->handle(
        serverId: 123,
        siteId: 456
    );
})->throws(RequestException::class);
