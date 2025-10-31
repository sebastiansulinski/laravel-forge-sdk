<?php

namespace SebastianSulinski\LaravelForgeSdk\Actions;

use SebastianSulinski\LaravelForgeSdk\Client;
use SebastianSulinski\LaravelForgeSdk\Data\Deployment;
use SebastianSulinski\LaravelForgeSdk\Exceptions\RequestFailed;
use SebastianSulinski\LaravelForgeSdk\Traits\HasDeployment;
use SebastianSulinski\LaravelForgeSdk\Traits\ParsesResponse;

/**
 * @phpstan-import-type DeploymentData from HasDeployment
 */
readonly class CreateDeployment
{
    use HasDeployment;
    use ParsesResponse;

    /**
     * CreateDeployment constructor.
     */
    public function __construct(private Client $client) {}

    /**
     * Handle request.
     *
     * @throws \Illuminate\Http\Client\ConnectionException
     * @throws \Illuminate\Http\Client\RequestException
     * @throws \SebastianSulinski\LaravelForgeSdk\Exceptions\RequestFailed
     */
    public function handle(int $serverId, int $siteId): Deployment
    {
        $path = $this->client->path(
            '/servers/%s/sites/%s/deployments', $serverId, $siteId
        );

        $response = $this->client->post($path)->throw();

        $data = $this->parseData($response);

        if (empty($data)) {
            throw new RequestFailed('Unable to create deployment.');
        }

        /** @var DeploymentData $data */
        return $this->makeDeployment($serverId, $siteId, $data);
    }
}
