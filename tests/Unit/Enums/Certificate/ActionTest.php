<?php

use SebastianSulinski\LaravelForgeSdk\Enums\Certificate\Action;

it('has enable case', function () {
    expect(Action::Enable)->toBeInstanceOf(Action::class)
        ->and(Action::Enable->value)->toBe('enable');
});

it('has disable case', function () {
    expect(Action::Disable)->toBeInstanceOf(Action::class)
        ->and(Action::Disable->value)->toBe('disable');
});

it('can be created from value', function () {
    expect(Action::from('enable'))->toBe(Action::Enable)
        ->and(Action::from('disable'))->toBe(Action::Disable);
});

it('has exactly two cases', function () {
    $cases = Action::cases();

    expect($cases)->toHaveCount(2)
        ->and($cases)->toContain(Action::Enable)
        ->and($cases)->toContain(Action::Disable);
});

it('is a backed enum with string type', function () {
    $reflection = new ReflectionEnum(Action::class);

    expect($reflection->isBacked())->toBeTrue()
        ->and($reflection->getBackingType()->getName())->toBe('string');
});
