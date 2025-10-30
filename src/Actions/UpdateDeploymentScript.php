<?php

namespace SebastianSulinski\LaravelForgeSdk\Actions;

use SebastianSulinski\LaravelForgeSdk\Client;
use SebastianSulinski\LaravelForgeSdk\Payload\UpdateDeploymentScriptPayload;

readonly class UpdateDeploymentScript
{
    /**
     * UpdateDeploymentScript constructor.
     */
    public function __construct(private Client $client) {}

    /**
     * Handle request.
     *
     * @throws \Illuminate\Http\Client\ConnectionException
     * @throws \Illuminate\Http\Client\RequestException
     * @throws \Exception
     */
    public function handle(int $serverId, int $siteId, UpdateDeploymentScriptPayload $payload): void
    {
        $path = $this->client->path(
            sprintf('/servers/%s/sites/%s/deployments/script', $serverId, $siteId)
        );

        $this->client->put($path, $payload->toArray())->throw();
    }
}
