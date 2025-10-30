<?php

namespace SebastianSulinski\LaravelForgeSdk\Actions;

use SebastianSulinski\LaravelForgeSdk\Client;

readonly class DeleteDatabaseUser
{
    public function __construct(private Client $client) {}

    /**
     * Handle request.
     *
     * @throws \Illuminate\Http\Client\ConnectionException
     * @throws \Illuminate\Http\Client\RequestException
     */
    public function handle(int $serverId, int $databaseUserId): void
    {
        $path = $this->client->path(
            '/servers/%s/database/users/%s', $serverId, $databaseUserId
        );

        $this->client->delete($path)->throw();
    }
}
