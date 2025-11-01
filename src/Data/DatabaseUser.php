<?php

namespace SebastianSulinski\LaravelForgeSdk\Data;

use Carbon\Carbon;
use SebastianSulinski\LaravelForgeSdk\Enums\Database\UserStatus;

readonly class DatabaseUser
{
    /**
     * DatabaseUser constructor.
     */
    public function __construct(
        public int $id,
        public int $serverId,
        public string $name,
        public UserStatus $status,
        public Carbon $createdAt,
        public Carbon $updatedAt,
    ) {}
}
