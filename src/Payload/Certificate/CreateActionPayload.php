<?php

namespace SebastianSulinski\LaravelForgeSdk\Payload\Certificate;

use Illuminate\Contracts\Support\Arrayable;
use SebastianSulinski\LaravelForgeSdk\Enums\Certificate\Action;

/**
 * @implements Arrayable<string, string>
 */
readonly class CreateActionPayload implements Arrayable
{
    /**
     * CreateActionPayload constructor.
     */
    public function __construct(
        public Action $action,
    ) {}

    /**
     * {@inheritDoc}
     */
    public function toArray(): array
    {
        return [
            'action' => $this->action->value,
        ];
    }
}
