<?php

namespace SebastianSulinski\LaravelForgeSdk\Data;

use SebastianSulinski\LaravelForgeSdk\Data\Concerns\HasLinks;

readonly class DeploymentScriptResource
{
    use HasLinks;

    /**
     * DeploymentScriptResource constructor.
     *
     * @param  array<string, mixed>  $links
     */
    public function __construct(
        public string $id,
        public ?string $content,
        public bool $autoSource,
        public array $links = [],
    ) {}
}
