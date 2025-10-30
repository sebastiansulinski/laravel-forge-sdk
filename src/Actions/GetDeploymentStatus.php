<?php

namespace SebastianSulinski\LaravelForgeSdk\Actions;

use Carbon\Carbon;
use Illuminate\Http\Client\Response;
use SebastianSulinski\LaravelForgeSdk\Client;
use SebastianSulinski\LaravelForgeSdk\Data\DeploymentStatus;
use SebastianSulinski\LaravelForgeSdk\Enums\DeploymentStatus as DeploymentStatusEnum;
use SebastianSulinski\LaravelForgeSdk\Exceptions\RequestFailed;

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

        $data = $this->responseData($response);

        if (empty($data)) {
            throw new RequestFailed('Unable to get deployment status.');
        }

        return $this->makeDeploymentStatus($serverId, $siteId, $data);
    }

    /**
     * Get the response data.
     *
     * @return DataArray|array{}
     */
    private function responseData(Response $response): array
    {
        $data = $response->json('data', []);

        return is_array($data) ? $data : [];
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
            status: DeploymentStatusEnum::from($attributes['status']),
            startedAt: Carbon::parse($attributes['started_at'])
        );
    }
}
