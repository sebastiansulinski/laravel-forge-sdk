<?php

namespace SebastianSulinski\LaravelForgeSdk\Payload;

use Illuminate\Contracts\Support\Arrayable;

readonly class CreateCommandPayload implements Arrayable
{
    /**
     * CreateCommandPayload constructor.
     */
    public function __construct(
        public string $command,
    ) {}

    /**
     * {@inheritDoc}
     */
    public function toArray(): array
    {
        return [
            'command' => $this->command,
        ];
    }
}
