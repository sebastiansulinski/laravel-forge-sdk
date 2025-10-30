<?php

namespace SebastianSulinski\LaravelForgeSdk\Data;

use SebastianSulinski\LaravelForgeSdk\Enums\RepositoryStatus;

readonly class Repository
{
    /**
     * Repository constructor.
     */
    public function __construct(
        public string $provider,
        public ?string $url = null,
        public ?string $branch = null,
        public ?RepositoryStatus $status = null,
    ) {}
}
