<?php

namespace SebastianSulinski\LaravelForgeSdk\Data;

use Carbon\Carbon;
use SebastianSulinski\LaravelForgeSdk\Enums\Deployment\Status as DeploymentStatusEnum;

readonly class DeploymentStatus
{
    /**
     * DeploymentStatus constructor.
     */
    public function __construct(
        public string $id,
        public int $serverId,
        public int $siteId,
        public Carbon $startedAt,
        public ?DeploymentStatusEnum $status = null,
    ) {}
}
