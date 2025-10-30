<?php

namespace SebastianSulinski\LaravelForgeSdk\Payload;

use Illuminate\Contracts\Support\Arrayable;

readonly class UpdateDeploymentScriptPayload implements Arrayable
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
