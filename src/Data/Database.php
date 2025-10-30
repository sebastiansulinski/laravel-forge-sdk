<?php

namespace SebastianSulinski\LaravelForgeSdk\Data;

use SebastianSulinski\LaravelForgeSdk\Enums\DatabaseStatus;
use Carbon\Carbon;

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
