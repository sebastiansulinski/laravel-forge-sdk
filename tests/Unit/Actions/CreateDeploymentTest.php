<?php

use Illuminate\Http\Client\Request;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Http;
use SebastianSulinski\LaravelForgeSdk\Actions\CreateDeployment;
use SebastianSulinski\LaravelForgeSdk\Client;
use SebastianSulinski\LaravelForgeSdk\Exceptions\RequestFailed;

beforeEach(function () {
    config()->set('forge.token', 'test-token');
    config()->set('forge.timeout', 90);
    config()->set('forge.organisation', 'test-org');
});

it('creates a deployment', function () {
    Http::fake([
        'forge.laravel.com/api/orgs/test-org/servers/123/sites/456/deployments' => Http::response([
            'data' => [
                'id' => 789,
                'attributes' => [
                    'commit' => [
                        'hash' => 'abc123def456',
                        'author' => 'John Doe',
                        'message' => 'Fix bug in authentication',
                        'branch' => 'main',
                    ],
                    'type' => 'git',
                    'status' => 'pending',
                    'created_at' => '2024-01-15T10:30:00.000000Z',
                    'updated_at' => '2024-01-15T10:30:00.000000Z',
                    'started_at' => null,
                    'ended_at' => null,
                ],
            ],
        ], 201),
    ]);

    $client = app(Client::class);
    $action = new CreateDeployment($client);

    $deployment = $action->handle(
        serverId: 123,
        siteId: 456
    );

    expect($deployment->id)->toBe(789)
        ->and($deployment->serverId)->toBe(123)
        ->and($deployment->siteId)->toBe(456)
        ->and($deployment->commit->hash)->toBe('abc123def456')
        ->and($deployment->commit->author)->toBe('John Doe')
        ->and($deployment->commit->message)->toBe('Fix bug in authentication')
        ->and($deployment->commit->branch)->toBe('main')
        ->and($deployment->type)->toBe('git')
        ->and($deployment->status->value)->toBe('pending');

    Http::assertSent(function (Request $request) {
        return $request->url() === 'https://forge.laravel.com/api/orgs/test-org/servers/123/sites/456/deployments'
            && $request->method() === 'POST'
            && $request->hasHeader('Authorization', 'Bearer test-token')
            && $request->hasHeader('Accept', 'application/json')
            && $request->hasHeader('Content-Type', 'application/json');
    });
});

it('throws RequestFailed exception when response data is empty', function () {
    Http::fake([
        'forge.laravel.com/api/orgs/test-org/servers/123/sites/456/deployments' => Http::response([
            'data' => [],
        ]),
    ]);

    $client = app(Client::class);
    $action = new CreateDeployment($client);

    $action->handle(
        serverId: 123,
        siteId: 456
    );
})->throws(RequestFailed::class, 'Unable to create deployment.');

it('throws exception when request fails', function () {
    Http::fake([
        'forge.laravel.com/api/orgs/test-org/servers/123/sites/456/deployments' => Http::response([
            'message' => 'Server error',
        ], 500),
    ]);

    $client = app(Client::class);
    $action = new CreateDeployment($client);

    $action->handle(
        serverId: 123,
        siteId: 456
    );
})->throws(RequestException::class);
