<?php

namespace SebastianSulinski\LaravelForgeSdk\Traits;

use SebastianSulinski\LaravelForgeSdk\Data\Commit;
use SebastianSulinski\LaravelForgeSdk\Data\Deployment;
use SebastianSulinski\LaravelForgeSdk\Enums\DeploymentStatus;
use Carbon\Carbon;

trait HasDeployment
{
    /**
     * Make a deployment.
     */
    protected function makeDeployment(int $serverId, int $siteId, array $data): Deployment
    {
        $attributes = $data['attributes'];
        $commitData = $attributes['commit'];

        return new Deployment(
            id: $data['id'],
            serverId: $serverId,
            siteId: $siteId,
            commit: new Commit(
                hash: $commitData['hash'] ?? null,
                author: $commitData['author'] ?? null,
                message: $commitData['message'] ?? null,
                branch: $commitData['branch'] ?? null
            ),
            type: $attributes['type'],
            status: DeploymentStatus::from($attributes['status']),
            createdAt: isset($attributes['created_at'])
                ? Carbon::parse($attributes['created_at'])
                : null,
            updatedAt: isset($attributes['updated_at'])
                ? Carbon::parse($attributes['updated_at'])
                : null,
            startedAt: isset($attributes['started_at'])
                ? Carbon::parse($attributes['started_at'])
                : null,
            endedAt: isset($attributes['ended_at'])
                ? Carbon::parse($attributes['ended_at'])
                : null,
        );
    }
}
