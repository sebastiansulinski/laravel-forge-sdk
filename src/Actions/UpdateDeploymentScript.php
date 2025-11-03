<?php

namespace SebastianSulinski\LaravelForgeSdk\Actions;

use Illuminate\Http\Client\Response;
use SebastianSulinski\LaravelForgeSdk\Client;
use SebastianSulinski\LaravelForgeSdk\Data\DeploymentScriptResource;
use SebastianSulinski\LaravelForgeSdk\Exceptions\RequestFailed;
use SebastianSulinski\LaravelForgeSdk\Payload\Deployment\UpdateScriptPayload;
use SebastianSulinski\LaravelForgeSdk\Traits\HasDeploymentScriptResource;
use SebastianSulinski\LaravelForgeSdk\Traits\ParsesResponse;

/**
 * @phpstan-import-type DeploymentScriptResourceData from HasDeploymentScriptResource
 */
readonly class UpdateDeploymentScript
{
    use HasDeploymentScriptResource;
    use ParsesResponse;

    /**
     * UpdateDeploymentScript constructor.
     */
    public function __construct(private Client $client) {}

    /**
     * Handle request.
     *
     * @throws \Illuminate\Http\Client\ConnectionException
     * @throws \Illuminate\Http\Client\RequestException
     * @throws \SebastianSulinski\LaravelForgeSdk\Exceptions\RequestFailed
     */
    public function handle(int $serverId, int $siteId, UpdateScriptPayload $payload): DeploymentScriptResource
    {
        $path = $this->client->path(
            '/servers/%s/sites/%s/deployments/script', $serverId, $siteId
        );

        $response = $this->client->put($path, $payload->toArray())->throw();

        if (! $response->successful()) {
            throw new RequestFailed($this->exceptionMessage($response));
        }

        /** @var DeploymentScriptResourceData $data */
        $data = $this->parseData($response);

        return $this->makeDeploymentScriptResource($data);
    }

    /**
     * Get the exception message.
     */
    private function exceptionMessage(Response $response): string
    {
        $message = $response->json('message', 'Response returned: '.$response->status());

        return is_string($message) ? $message : 'Response returned: '.$response->status();
    }
}
