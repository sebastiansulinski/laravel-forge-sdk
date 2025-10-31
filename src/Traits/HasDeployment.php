<?php

namespace SebastianSulinski\LaravelForgeSdk\Traits;

use Carbon\Carbon;
use SebastianSulinski\LaravelForgeSdk\Data\Commit;
use SebastianSulinski\LaravelForgeSdk\Data\Deployment;
use SebastianSulinski\LaravelForgeSdk\Enums\DeploymentStatus;

/**
 * @phpstan-type DeploymentData array{
 *     id: int,
 *     attributes: array{
 *         commit: array{
 *             hash?: string|null,
 *             author?: string|null,
 *             message?: string|null,
 *             branch?: string|null
 *         },
 *         type: string,
 *         status: string,
 *         created_at?: string|null,
 *         updated_at?: string|null,
 *         started_at?: string|null,
 *         ended_at?: string|null
 *     }
 * }
 */
trait HasDeployment
{
    /**
     * Make a deployment.
     *
     * @param  DeploymentData  $data
     */
    protected function makeDeployment(int $siteId, array $data): Deployment
    {
        $attributes = $data['attributes'];
        $commitData = $attributes['commit'];

        return new Deployment(
            id: $data['id'],
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
