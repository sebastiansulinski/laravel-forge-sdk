<?php

namespace SebastianSulinski\LaravelForgeSdk\Data;

use Carbon\Carbon;
use SebastianSulinski\LaravelForgeSdk\Enums\DeploymentStatus;

readonly class Deployment
{
    /**
     * Deployment constructor.
     */
    public function __construct(
        public int $id,
        public int $serverId,
        public int $siteId,
        public Commit $commit,
        public string $type,
        public DeploymentStatus $status,
        public ?Carbon $createdAt = null,
        public ?Carbon $updatedAt = null,
        public ?Carbon $startedAt = null,
        public ?Carbon $endedAt = null,
    ) {}
}
