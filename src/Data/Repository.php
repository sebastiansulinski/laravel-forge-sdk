<?php

namespace SebastianSulinski\LaravelForgeSdk\Data;

use SebastianSulinski\LaravelForgeSdk\Enums\Repository\Status;

readonly class Repository
{
    /**
     * Repository constructor.
     */
    public function __construct(
        public string $provider,
        public ?string $url = null,
        public ?string $branch = null,
        public ?Status $status = null,
    ) {}
}
