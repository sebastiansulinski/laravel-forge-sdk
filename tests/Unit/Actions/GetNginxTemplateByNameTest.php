<?php

use Illuminate\Http\Client\Request;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Http;
use SebastianSulinski\LaravelForgeSdk\Actions\GetNginxTemplateByName;
use SebastianSulinski\LaravelForgeSdk\Client;

beforeEach(function () {
    config()->set('forge.token', 'test-token');
    config()->set('forge.timeout', 90);
    config()->set('forge.organisation', 'test-org');
});

it('gets nginx template by name', function () {
    Http::fake([
        'forge.laravel.com/api/orgs/test-org/servers/123/nginx/templates*' => Http::response([
            'data' => [
                [
                    'id' => 456,
                    'attributes' => [
                        'name' => 'custom-template',
                        'content' => 'server { listen 80; }',
                        'created_at' => '2024-01-15T10:30:00.000000Z',
                        'updated_at' => '2024-01-15T10:30:00.000000Z',
                    ],
                ],
            ],
        ]),
    ]);

    $client = app(Client::class);
    $action = new GetNginxTemplateByName($client);

    $template = $action->handle(
        serverId: 123,
        templateName: 'custom-template'
    );

    expect($template->id)->toBe(456)
        ->and($template->serverId)->toBe(123)
        ->and($template->name)->toBe('custom-template')
        ->and($template->content)->toBe('server { listen 80; }');

    Http::assertSent(function (Request $request) {
        return str_contains($request->url(), 'https://forge.laravel.com/api/orgs/test-org/servers/123/nginx/templates')
            && $request->method() === 'GET'
            && $request->data()['filter[name]'] === 'custom-template'
            && $request->hasHeader('Authorization', 'Bearer test-token')
            && $request->hasHeader('Accept', 'application/json')
            && $request->hasHeader('Content-Type', 'application/json');
    });
});

it('returns null when template not found', function () {
    Http::fake([
        'forge.laravel.com/api/orgs/test-org/servers/123/nginx/templates*' => Http::response([
            'data' => [],
        ]),
    ]);

    $client = app(Client::class);
    $action = new GetNginxTemplateByName($client);

    $template = $action->handle(
        serverId: 123,
        templateName: 'nonexistent'
    );

    expect($template)->toBeNull();
});

it('throws exception when request fails', function () {
    Http::fake([
        'forge.laravel.com/api/orgs/test-org/servers/123/nginx/templates*' => Http::response([
            'message' => 'Server error',
        ], 500),
    ]);

    $client = app(Client::class);
    $action = new GetNginxTemplateByName($client);

    $action->handle(
        serverId: 123,
        templateName: 'custom-template'
    );
})->throws(RequestException::class);
