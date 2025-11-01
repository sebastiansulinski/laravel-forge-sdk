<?php

namespace SebastianSulinski\LaravelForgeSdk\Actions;

use SebastianSulinski\LaravelForgeSdk\Client;
use SebastianSulinski\LaravelForgeSdk\Payload\Server\CreateCommandPayload;

readonly class CreateCommand
{
    /**
     * CreateCommand constructor.
     */
    public function __construct(private Client $client) {}

    /**
     * Handle request.
     *
     * @throws \Illuminate\Http\Client\ConnectionException
     * @throws \Illuminate\Http\Client\RequestException
     * @throws \Exception
     */
    public function handle(int $serverId, int $siteId, CreateCommandPayload $payload): void
    {
        $path = $this->client->path(
            '/servers/%s/sites/%s/commands', $serverId, $siteId
        );

        $this->client->post($path, $payload->toArray())->throw();
    }
}
