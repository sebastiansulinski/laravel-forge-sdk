<?php

use SebastianSulinski\LaravelForgeSdk\Enums\Certificate\Action;
use SebastianSulinski\LaravelForgeSdk\Payload\Certificate\CreateActionPayload;

it('creates payload with enable action', function () {
    $payload = new CreateActionPayload(action: Action::Enable);

    expect($payload->action)->toBe(Action::Enable)
        ->and($payload->toArray())->toBe([
            'action' => 'enable',
        ]);
});

it('creates payload with disable action', function () {
    $payload = new CreateActionPayload(action: Action::Disable);

    expect($payload->action)->toBe(Action::Disable)
        ->and($payload->toArray())->toBe([
            'action' => 'disable',
        ]);
});

it('is readonly', function () {
    $payload = new CreateActionPayload(action: Action::Enable);

    expect($payload)->toBeInstanceOf(CreateActionPayload::class);

    // Attempting to modify a readonly property should cause an error
    $reflection = new ReflectionClass($payload);
    expect($reflection->isReadOnly())->toBeTrue();
});

it('implements Arrayable interface', function () {
    $payload = new CreateActionPayload(action: Action::Enable);

    expect($payload)->toBeInstanceOf(Illuminate\Contracts\Support\Arrayable::class);
});
