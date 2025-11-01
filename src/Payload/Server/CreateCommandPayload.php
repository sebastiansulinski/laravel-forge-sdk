<?php

namespace SebastianSulinski\LaravelForgeSdk\Payload\Server;

use Illuminate\Contracts\Support\Arrayable;

/**
 * @implements Arrayable<string, string>
 */
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
