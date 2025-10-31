<?php

namespace SebastianSulinski\LaravelForgeSdk\Data;

use Carbon\Carbon;

readonly class NginxTemplate
{
    /**
     * NginxTemplate constructor.
     */
    public function __construct(
        public int $id,
        public string $name,
        public string $content,
        public ?Carbon $createdAt = null,
        public ?Carbon $updatedAt = null,
    ) {}
}
