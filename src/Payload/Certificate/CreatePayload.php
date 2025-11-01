<?php

namespace SebastianSulinski\LaravelForgeSdk\Payload\Certificate;

use Illuminate\Contracts\Support\Arrayable;
use SebastianSulinski\LaravelForgeSdk\Enums\Certificate\Type;

/**
 * @implements Arrayable<string, mixed>
 */
abstract readonly class CreatePayload implements Arrayable
{
    /**
     * Get certificate type.
     */
    abstract public function type(): Type;
}
