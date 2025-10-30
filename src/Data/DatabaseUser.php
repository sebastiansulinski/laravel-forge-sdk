<?php

namespace SebastianSulinski\LaravelForgeSdk\Data;

use SebastianSulinski\LaravelForgeSdk\Enums\DatabaseUserStatus;
use Carbon\Carbon;

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
