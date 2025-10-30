<?php

namespace SebastianSulinski\LaravelForgeSdk\Payload;

readonly class PaginationParameters
{
    /**
     * PaginationParameters constructor.
     */
    public function __construct(
        public ?string $sort = null,
        public ?int $pageSize = null,
        public ?string $pageCursor = null,
    ) {}
}
