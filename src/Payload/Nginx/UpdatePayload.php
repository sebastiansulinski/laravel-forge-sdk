<?php

namespace SebastianSulinski\LaravelForgeSdk\Payload\Nginx;

use Illuminate\Contracts\Support\Arrayable;

/**
 * @implements Arrayable<string, mixed>
 */
readonly class UpdatePayload implements Arrayable
{
    /**
     * UpdatePayload constructor.
     */
    public function __construct(
        public string $config,
    ) {}

    /**
     * {@inheritDoc}
     */
    public function toArray(): array
    {
        return [
            'config' => $this->config,
        ];
    }
}
