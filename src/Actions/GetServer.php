<?php

namespace SebastianSulinski\LaravelForgeSdk\Actions;

use SebastianSulinski\LaravelForgeSdk\Client;
use SebastianSulinski\LaravelForgeSdk\Data\Server;
use SebastianSulinski\LaravelForgeSdk\Exceptions\RequestFailed;
use SebastianSulinski\LaravelForgeSdk\Traits\HasServer;

readonly class GetServer
{
    use HasServer;

    /**
     * GetServer constructor.
     */
    public function __construct(private Client $client) {}

    /**
     * Handle request.
     *
     * @throws \Illuminate\Http\Client\ConnectionException
     * @throws \Illuminate\Http\Client\RequestException
     * @throws \SebastianSulinski\LaravelForgeSdk\Exceptions\RequestFailed
     */
    public function handle(int $serverId): Server
    {
        $path = $this->client->path(sprintf('/servers/%s', $serverId));

        $response = $this->client->get($path)->throw();

        $data = $response->json('data', []);

        if (empty($data)) {
            throw new RequestFailed('Unable to get server.');
        }

        return $this->makeServer($data);
    }
}
