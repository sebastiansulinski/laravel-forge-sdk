<?php

namespace SebastianSulinski\LaravelForgeSdk\Actions;

use SebastianSulinski\LaravelForgeSdk\Client;

readonly class DeleteSite
{
    /**
     * DeleteSite constructor.
     */
    public function __construct(private Client $client) {}

    /**
     * Handle request.
     *
     * @throws \Illuminate\Http\Client\ConnectionException
     * @throws \Illuminate\Http\Client\RequestException
     */
    public function handle(int $serverId, int $siteId): void
    {
        $path = $this->client->path(
            sprintf('/servers/%s/sites/%s', $serverId, $siteId)
        );

        $this->client->delete($path)->throw();
    }
}
