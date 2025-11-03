<?php

namespace SebastianSulinski\LaravelForgeSdk\Actions;

use SebastianSulinski\LaravelForgeSdk\Client;
use SebastianSulinski\LaravelForgeSdk\Payload\Site\UpdatePayload;

readonly class UpdateSite
{
    /**
     * UpdateSite constructor.
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
            '/servers/%s/sites/%s', $serverId, $siteId
        );

        $response = $this->client->put($path, $payload->toArray())->throw();

        return $response->status() === 202;
    }
}
