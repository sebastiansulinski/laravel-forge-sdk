<?php

namespace SebastianSulinski\LaravelForgeSdk\Actions;

use Carbon\Carbon;
use SebastianSulinski\LaravelForgeSdk\Client;
use SebastianSulinski\LaravelForgeSdk\Data\DeploymentStatus;
use SebastianSulinski\LaravelForgeSdk\Enums\DeploymentStatus as DeploymentStatusEnum;
use SebastianSulinski\LaravelForgeSdk\Exceptions\RequestFailed;
use SebastianSulinski\LaravelForgeSdk\Traits\ParsesResponse;

/**
 * @phpstan-type DataArray array{
 *     id: string,
 *     attributes: array{
 *         status: string,
 *         started_at: string
 *     }
 * }
 */
readonly class GetDeploymentStatus
{
    use ParsesResponse;

    /**
     * GetDeploymentStatus constructor.
     */
    public function __construct(private Client $client) {}

    /**
     * Handle request.
     *
     * @throws \Illuminate\Http\Client\ConnectionException
     * @throws \Illuminate\Http\Client\RequestException
     * @throws \SebastianSulinski\LaravelForgeSdk\Exceptions\RequestFailed
     */
    public function handle(int $serverId, int $siteId): DeploymentStatus
    {
        $path = $this->client->path(
            '/servers/%s/sites/%s/deployments/status', $serverId, $siteId
        );

        $response = $this->client->get($path)->throw();

        $data = $this->parseData($response);

        if (empty($data)) {
            throw new RequestFailed('Unable to get deployment status.');
        }

        /** @var DataArray $data */
        return $this->makeDeploymentStatus($serverId, $siteId, $data);
    }

    /**
     * Make a deployment status.
     *
     * @param  DataArray  $data
     */
    private function makeDeploymentStatus(int $serverId, int $siteId, array $data): DeploymentStatus
    {
        $attributes = $data['attributes'];

        return new DeploymentStatus(
            id: $data['id'],
            serverId: $serverId,
            siteId: $siteId,
            status: $this->status($attributes['status']),
            startedAt: Carbon::parse($attributes['started_at'])
        );
    }

    /**
     * Get status.
     */
    private function status(?string $status = null): DeploymentStatusEnum
    {
        return $status === null
            ? DeploymentStatusEnum::Pending
            : DeploymentStatusEnum::from($status);
    }
}
