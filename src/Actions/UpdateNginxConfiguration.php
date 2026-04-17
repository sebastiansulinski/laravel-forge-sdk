<?php

namespace SebastianSulinski\LaravelForgeSdk\Actions;

use SebastianSulinski\LaravelForgeSdk\Client;
use SebastianSulinski\LaravelForgeSdk\Payload\Nginx\UpdatePayload;

readonly class UpdateNginxConfiguration
{
    /**
     * UpdateNginxConfiguration constructor.
     */
    public function __construct(private Client $client) {}

    /**
     * Handle request.
     *
     * @throws \Illuminate\Http\Client\ConnectionException
     * @throws \Illuminate\Http\Client\RequestException
     */
    public function handle(int $serverId, int $siteId, UpdatePayload $payload): bool
    {
        $path = $this->client->path(
            '/servers/%s/sites/%s/nginx',
            $serverId,
            $siteId
        );

        $response = $this->client->put($path, $payload->toArray())->throw();

        return $response->status() === 202;
    }
}
