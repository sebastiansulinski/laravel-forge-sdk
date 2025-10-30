<?php

namespace SebastianSulinski\LaravelForgeSdk\Data;

use Carbon\Carbon;
use SebastianSulinski\LaravelForgeSdk\Enums\DatabaseStatus;

readonly class Database
{
    /**
     * Database constructor.
     */
    public function __construct(
        public int $id,
        public int $serverId,
        public string $name,
        public DatabaseStatus $status,
        public ?Carbon $createdAt = null,
        public ?Carbon $updatedAt = null,
    ) {}
}
