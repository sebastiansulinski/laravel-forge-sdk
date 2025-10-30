<?php

namespace SebastianSulinski\LaravelForgeSdk\Actions;

use SebastianSulinski\LaravelForgeSdk\Client;
use SebastianSulinski\LaravelForgeSdk\Payload\UpdateEnvContentPayload;

readonly class UpdateEnvContent
{
    /**
     * UpdateEnvContent constructor.
     */
    public function __construct(private Client $client) {}

    /**
     * Handle request.
     *
     * @throws \Illuminate\Http\Client\ConnectionException
     * @throws \Illuminate\Http\Client\RequestException
     * @throws \Exception
     */
    public function handle(int $serverId, int $siteId, UpdateEnvContentPayload $payload): void
    {
        $path = $this->client->path(
            '/servers/%s/sites/%s/environment', $serverId, $siteId
        );

        $this->client->put($path, $payload->toArray())->throw();
    }
}
