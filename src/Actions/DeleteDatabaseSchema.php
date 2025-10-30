<?php

namespace SebastianSulinski\LaravelForgeSdk\Actions;

use SebastianSulinski\LaravelForgeSdk\Client;

readonly class DeleteDatabaseSchema
{
    /**
     * DeleteDatabaseSchema constructor.
     */
    public function __construct(private Client $client) {}

    /**
     * Handle request.
     *
     * @throws \Illuminate\Http\Client\ConnectionException
     * @throws \Illuminate\Http\Client\RequestException
     */
    public function handle(int $serverId, int $databaseId): void
    {
        $path = $this->client->path(
            '/servers/%s/database/schemas/%s', $serverId, $databaseId
        );

        $this->client->delete($path)->throw();
    }
}
