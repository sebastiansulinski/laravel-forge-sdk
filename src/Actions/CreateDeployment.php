<?php

namespace SebastianSulinski\LaravelForgeSdk\Actions;

use SebastianSulinski\LaravelForgeSdk\Client;
use SebastianSulinski\LaravelForgeSdk\Data\Deployment;
use SebastianSulinski\LaravelForgeSdk\Exceptions\RequestFailed;
use SebastianSulinski\LaravelForgeSdk\Traits\HasDeployment;

readonly class CreateDeployment
{
    use HasDeployment;

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
            sprintf('/servers/%s/sites/%s/deployments', $serverId, $siteId)
        );

        $response = $this->client->post($path)->throw();

        $data = $response->json('data', []);

        if (empty($data)) {
            throw new RequestFailed('Unable to create deployment.');
        }

        return $this->makeDeployment($serverId, $siteId, $data);
    }
}
