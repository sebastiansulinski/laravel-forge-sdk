<?php

namespace SebastianSulinski\LaravelForgeSdk\Actions;

use SebastianSulinski\LaravelForgeSdk\Client;

readonly class GetEnvContent
{
    /**
     * GetEnvContent constructor.
     */
    public function __construct(private Client $client) {}

    /**
     * Handle request.
     *
     * @throws \Illuminate\Http\Client\ConnectionException
     * @throws \Illuminate\Http\Client\RequestException
     */
    public function handle(int $serverId, int $siteId): string
    {
        $path = $this->client->path(
            sprintf('/servers/%s/sites/%s/environment', $serverId, $siteId)
        );

        $response = $this->client->get($path)->throw();

        return $response->json('data.attributes.content', '');
    }
}
