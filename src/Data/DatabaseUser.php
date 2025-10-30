<?php

namespace SebastianSulinski\LaravelForgeSdk\Data;

use Carbon\Carbon;
use SebastianSulinski\LaravelForgeSdk\Enums\DatabaseUserStatus;

readonly class DatabaseUser
{
    /**
     * DatabaseUser constructor.
     */
    public function __construct(
        public int $id,
        public int $serverId,
        public string $name,
        public DatabaseUserStatus $status,
        public Carbon $createdAt,
        public Carbon $updatedAt,
    ) {}
}
