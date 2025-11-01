<?php

namespace SebastianSulinski\LaravelForgeSdk\Data;

use Carbon\Carbon;
use SebastianSulinski\LaravelForgeSdk\Enums\Command\Status;

readonly class Command
{
    /**
     * Command constructor.
     */
    public function __construct(
        public int $id,
        public string $command,
        public Status $status,
        public ?int $userId = null,
        public ?string $duration = null,
        public ?Carbon $createdAt = null,
        public ?Carbon $updatedAt = null,
    ) {}
}
