<?php

namespace SebastianSulinski\LaravelForgeSdk\Actions;

use Illuminate\Http\Client\Response;
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
            '/servers/%s/sites/%s/deployments', $serverId, $siteId
        );

        $response = $this->client->post($path)->throw();

        $data = $this->responseData($response);

        if (empty($data)) {
            throw new RequestFailed('Unable to create deployment.');
        }

        return $this->makeDeployment($serverId, $siteId, $data);
    }

    /**
     * Get the response data.
     *
     * @return array<string, mixed>
     */
    private function responseData(Response $response): array
    {
        $data = $response->json('data', []);

        return is_array($data) ? $data : [];
    }
}
