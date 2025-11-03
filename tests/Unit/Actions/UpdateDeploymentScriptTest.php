<?php

use Illuminate\Http\Client\Request;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Http;
use SebastianSulinski\LaravelForgeSdk\Actions\UpdateDeploymentScript;
use SebastianSulinski\LaravelForgeSdk\Client;
use SebastianSulinski\LaravelForgeSdk\Payload\Deployment\UpdateScriptPayload;

beforeEach(function () {
    config()->set('forge.token', 'test-token');
    config()->set('forge.timeout', 90);
    config()->set('forge.organisation', 'test-org');
});

it('updates deployment script', function () {
    $scriptContent = implode(PHP_EOL, [
        'cd /home/forge/example.com',
        'git pull origin production',
        'composer install --no-dev --optimize-autoloader',
        'php artisan migrate --force',
        'php artisan config:cache',
    ]);

    Http::fake([
        'forge.laravel.com/api/orgs/test-org/servers/123/sites/456/deployments/script' => Http::response([
            'data' => [
                'id' => '12345',
                'type' => 'deploymentScripts',
                'attributes' => [
                    'content' => $scriptContent,
                    'auto_source' => true,
                ],
                'links' => [
                    'self' => 'https://forge.laravel.com/api/orgs/test-org/servers/123/sites/456/deployments/script',
                ],
            ],
        ], 200),
    ]);

    $client = app(Client::class);
    $action = new UpdateDeploymentScript($client);

    $payload = new UpdateScriptPayload(
        content: $scriptContent,
        auto_source: true
    );

    $result = $action->handle(
        serverId: 123,
        siteId: 456,
        payload: $payload
    );

    expect($result->id)->toBe('12345')
        ->and($result->content)->toBe($scriptContent)
        ->and($result->autoSource)->toBeTrue();

    Http::assertSent(function (Request $request) use ($payload) {
        return $request->url() === 'https://forge.laravel.com/api/orgs/test-org/servers/123/sites/456/deployments/script'
            && $request->method() === 'PUT'
            && $request['content'] === $payload->content
            && $request['auto_source'] === true
            && $request->hasHeader('Authorization', 'Bearer test-token')
            && $request->hasHeader('Accept', 'application/json')
            && $request->hasHeader('Content-Type', 'application/json');
    });
});

it('throws exception when request fails', function () {
    Http::fake([
        'forge.laravel.com/api/orgs/test-org/servers/123/sites/456/deployments/script' => Http::response([
            'message' => 'Server error',
        ], 500),
    ]);

    $client = app(Client::class);
    $action = new UpdateDeploymentScript($client);

    $payload = new UpdateScriptPayload(
        content: 'cd /home/forge/example.com'
    );

    $action->handle(
        serverId: 123,
        siteId: 456,
        payload: $payload
    );
})->throws(RequestException::class);
