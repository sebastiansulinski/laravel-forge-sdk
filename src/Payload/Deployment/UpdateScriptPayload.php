<?php

namespace SebastianSulinski\LaravelForgeSdk\Payload\Deployment;

use Illuminate\Contracts\Support\Arrayable;

/**
 * @implements Arrayable<string, mixed>
 */
readonly class UpdateScriptPayload implements Arrayable
{
    /**
     * UpdateDeploymentScriptPayload constructor.
     */
    public function __construct(
        public string $content,
        public ?bool $auto_source = null,
    ) {}

    /**
     * {@inheritDoc}
     */
    public function toArray(): array
    {
        return array_filter([
            'content' => $this->content,
            'auto_source' => $this->auto_source,
        ], fn ($value) => $value !== null);
    }
}
