<?php

namespace SebastianSulinski\LaravelForgeSdk\Actions;

use SebastianSulinski\LaravelForgeSdk\Client;
use SebastianSulinski\LaravelForgeSdk\Data\Server;
use SebastianSulinski\LaravelForgeSdk\Exceptions\RequestFailed;
use SebastianSulinski\LaravelForgeSdk\Traits\HasServer;
use SebastianSulinski\LaravelForgeSdk\Traits\ParsesResponse;

/**
 * @phpstan-import-type ServerData from HasServer
 */
readonly class GetServer
{
    use HasServer;
    use ParsesResponse;

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
        $path = $this->client->path('/servers/%s', $serverId);

        $httpResponse = $this->client->get($path)->throw();

        $data = $this->parseData($httpResponse);

        if (empty($data)) {
            throw new RequestFailed('Unable to get server.');
        }

        /** @var ServerData $data */
        return $this->makeServer($data);
    }
}
