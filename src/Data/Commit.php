<?php

namespace SebastianSulinski\LaravelForgeSdk\Data;

readonly class Commit
{
    /**
     * Commit constructor.
     */
    public function __construct(
        public ?string $hash = null,
        public ?string $author = null,
        public ?string $message = null,
        public ?string $branch = null,
    ) {}
}
