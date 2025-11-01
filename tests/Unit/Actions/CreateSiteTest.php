<?php

use Illuminate\Http\Client\Request;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Http;
use SebastianSulinski\LaravelForgeSdk\Actions\CreateSite;
use SebastianSulinski\LaravelForgeSdk\Client;
use SebastianSulinski\LaravelForgeSdk\Enums\Repository\Provider;
use SebastianSulinski\LaravelForgeSdk\Enums\Server\PhpVersion;
use SebastianSulinski\LaravelForgeSdk\Enums\Site\DomainMode;
use SebastianSulinski\LaravelForgeSdk\Enums\Site\Type;
use SebastianSulinski\LaravelForgeSdk\Payload\Site\CreatePayload;

beforeEach(function () {
    config()->set('forge.token', 'test-token');
    config()->set('forge.timeout', 90);
    config()->set('forge.organisation', 'test-org');
});

it('creates a site with explicit nginx template id', function () {
    Http::fake([
        'forge.laravel.com/api/orgs/test-org/servers/123/sites' => Http::response([
            'data' => [
                'id' => 456,
                'attributes' => [
                    'name' => 'example.com',
                    'status' => 'installing',
                    'url' => 'https://example.com',
                    'user' => 'forge',
                    'https' => true,
                    'web_directory' => '/public',
                    'root_directory' => '/home/forge/example.com',
                    'aliases' => [],
                    'php_version' => 'php83',
                    'deployment_status' => null,
                    'isolated' => false,
                    'shared_paths' => [],
                    'zero_downtime_deployments' => false,
                    'app_type' => 'php',
                    'uses_envoyer' => false,
                    'deployment_url' => '',
                    'repository' => [
                        'provider' => 'github',
                        'url' => 'https://github.com/user/repo',
                        'branch' => 'main',
                        'status' => 'installed',
                    ],
                    'maintenance_mode' => [
                        'enabled' => false,
                        'status' => null,
                    ],
                    'quick_deploy' => false,
                    'deployment_script' => '',
                    'wildcards' => false,
                    'healthcheck_url' => null,
                    'created_at' => '2024-01-15T10:30:00.000000Z',
                    'updated_at' => '2024-01-15T10:30:00.000000Z',
                ],
            ],
        ], 201),
    ]);

    $client = app(Client::class);
    $action = new CreateSite($client);

    $payload = new CreatePayload(
        type: Type::Php,
        name: 'example.com',
        domain_mode: DomainMode::Custom,
        web_directory: '/public',
        php_version: PhpVersion::Php83,
        source_control_provider: Provider::Github,
        repository: 'user/repo',
        branch: 'main',
        nginx_template_id: 999
    );

    $site = $action->handle(
        serverId: 123,
        payload: $payload
    );

    expect($site->id)->toBe(456)
        ->and($site->name)->toBe('example.com')
        ->and($site->status->value)->toBe('installing')
        ->and($site->url)->toBe('https://example.com')
        ->and($site->webDirectory)->toBe('/public')
        ->and($site->phpVersion)->toBe('php83')
        ->and($site->repository->provider)->toBe('github')
        ->and($site->repository->branch)->toBe('main');

    Http::assertSent(function (Request $request) {
        return $request->url() === 'https://forge.laravel.com/api/orgs/test-org/servers/123/sites'
            && $request->method() === 'POST'
            && $request['name'] === 'example.com'
            && $request['type'] === 'php'
            && $request['web_directory'] === '/public'
            && $request['php_version'] === 'php83'
            && $request['source_control_provider'] === 'github'
            && $request['repository'] === 'user/repo'
            && $request['branch'] === 'main'
            && $request['nginx_template_id'] === 999
            && $request->hasHeader('Authorization', 'Bearer test-token')
            && $request->hasHeader('Accept', 'application/json')
            && $request->hasHeader('Content-Type', 'application/json');
    });
});

it('throws exception when request fails', function () {
    Http::fake([
        'forge.laravel.com/api/orgs/test-org/servers/123/sites' => Http::response([
            'message' => 'Server error',
        ], 500),
    ]);

    $client = app(Client::class);
    $action = new CreateSite($client);

    $payload = new CreatePayload(
        type: Type::Php,
        name: 'example.com',
        domain_mode: DomainMode::Custom,
        web_directory: '/public',
        php_version: PhpVersion::Php83,
        source_control_provider: Provider::Github,
        repository: 'user/repo',
        branch: 'main',
        nginx_template_id: 999
    );

    $action->handle(
        serverId: 123,
        payload: $payload
    );
})->throws(RequestException::class);
