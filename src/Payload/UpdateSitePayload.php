<?php

namespace SebastianSulinski\LaravelForgeSdk\Payload;

use Illuminate\Contracts\Support\Arrayable;
use SebastianSulinski\LaravelForgeSdk\Enums\PhpVersion;
use SebastianSulinski\LaravelForgeSdk\Enums\SiteType;

/**
 * @implements Arrayable<string, mixed>
 */
readonly class UpdateSitePayload implements Arrayable
{
    /**
     * UpdateSitePayload constructor.
     */
    public function __construct(
        public ?string $directory = null,
        public ?SiteType $type = null,
        public ?PhpVersion $php_version = null,
        public ?bool $push_to_deploy = null,
        public ?string $repository_branch = null,
    ) {}

    /**
     * {@inheritDoc}
     */
    public function toArray(): array
    {
        return array_filter([
            'directory' => $this->directory,
            'type' => $this->type?->value,
            'php_version' => $this->php_version?->value,
            'push_to_deploy' => $this->push_to_deploy,
            'repository_branch' => $this->repository_branch,
        ], fn ($value) => $value !== null);
    }
}
