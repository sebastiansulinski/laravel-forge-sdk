<?php

namespace SebastianSulinski\LaravelForgeSdk\Data;

use Carbon\Carbon;
use SebastianSulinski\LaravelForgeSdk\Enums\DeploymentStatus as DeploymentStatusEnum;

readonly class DeploymentStatus
{
    /**
     * DeploymentStatus constructor.
     */
    public function __construct(
        public string $id,
        public int $serverId,
        public int $siteId,
        public DeploymentStatusEnum $status,
        public Carbon $startedAt,
    ) {}
}
