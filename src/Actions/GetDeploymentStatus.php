<?php

namespace SebastianSulinski\LaravelForgeSdk\Actions;

use SebastianSulinski\LaravelForgeSdk\Client;
use SebastianSulinski\LaravelForgeSdk\Data\DeploymentStatus;
use SebastianSulinski\LaravelForgeSdk\Enums\DeploymentStatus as DeploymentStatusEnum;
use SebastianSulinski\LaravelForgeSdk\Exceptions\RequestFailed;
use Carbon\Carbon;

readonly class GetDeploymentStatus
{
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
            sprintf('/servers/%s/sites/%s/deployments/status', $serverId, $siteId)
        );

        $response = $this->client->get($path)->throw();

        $data = $response->json('data', []);

        if (empty($data)) {
            throw new RequestFailed('Unable to get deployment status.');
        }

        return $this->makeDeploymentStatus($serverId, $siteId, $data);
    }

    /**
     * Make a deployment status.
     */
    private function makeDeploymentStatus(int $serverId, int $siteId, array $data): DeploymentStatus
    {
        $attributes = $data['attributes'];

        return new DeploymentStatus(
            id: $data['id'],
            serverId: $serverId,
            siteId: $siteId,
            status: DeploymentStatusEnum::from($attributes['status']),
            startedAt: Carbon::parse($attributes['started_at'])
        );
    }
}
