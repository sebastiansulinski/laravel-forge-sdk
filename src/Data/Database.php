<?php

namespace SebastianSulinski\LaravelForgeSdk\Data;

use Carbon\Carbon;
use SebastianSulinski\LaravelForgeSdk\Enums\Database\Status;

readonly class Database
{
    /**
     * Database constructor.
     */
    public function __construct(
        public int $id,
        public int $serverId,
        public string $name,
        public Status $status,
        public ?Carbon $createdAt = null,
        public ?Carbon $updatedAt = null,
    ) {}
}
