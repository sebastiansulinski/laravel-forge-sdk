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
    public function handle(int $serverId, int $siteId): bool
    {
        $path = $this->client->path(
            '/servers/%s/sites/%s', $serverId, $siteId
        );

        $response = $this->client->delete($path)->throw();

        return $response->status() === 202;
    }
}
