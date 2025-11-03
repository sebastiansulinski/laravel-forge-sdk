<?php

namespace SebastianSulinski\LaravelForgeSdk\Data;

use SebastianSulinski\LaravelForgeSdk\Data\Concerns\HasApiMetadata;

readonly class DeploymentScriptResource
{
    use HasApiMetadata;

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
