<?php

namespace SebastianSulinski\LaravelForgeSdk\Payload;

use Illuminate\Contracts\Support\Arrayable;

readonly class UpdateEnvContentPayload implements Arrayable
{
    /**
     * UpdateEnvContentPayload constructor.
     */
    public function __construct(
        public string $environment,
        public ?bool $cache = null,
        public ?bool $queues = null,
        public ?string $encryption_key = null,
    ) {}

    /**
     * {@inheritDoc}
     */
    public function toArray(): array
    {
        return array_filter([
            'environment' => $this->environment,
            'cache' => $this->cache,
            'queues' => $this->queues,
            'encryption_key' => $this->encryption_key,
        ], fn ($value) => $value !== null);
    }
}
