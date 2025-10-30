<?php

namespace SebastianSulinski\LaravelForgeSdk\Actions;

use SebastianSulinski\LaravelForgeSdk\Client;
use SebastianSulinski\LaravelForgeSdk\Data\Database;
use SebastianSulinski\LaravelForgeSdk\Exceptions\RequestFailed;
use SebastianSulinski\LaravelForgeSdk\Payload\CreateDatabasePayload;
use SebastianSulinski\LaravelForgeSdk\Traits\HasDatabase;

readonly class CreateDatabase
{
    use HasDatabase;

    /**
     * CreateDatabase constructor.
     */
    public function __construct(private Client $client) {}

    /**
     * Handle request.
     *
     * @throws \SebastianSulinski\LaravelForgeSdk\Exceptions\RequestFailed
     * @throws \Illuminate\Http\Client\ConnectionException
     * @throws \Illuminate\Http\Client\RequestException
     * @throws \Exception
     */
    public function handle(int $serverId, CreateDatabasePayload $payload): Database
    {
        $path = $this->client->path(sprintf('/servers/%s/database/schemas', $serverId));

        $response = $this->client->post($path, $payload->toArray())->throw();

        if (! $response->successful()) {
            throw new RequestFailed(
                $response->json('message', 'Response returned: '.$response->status())
            );
        }

        return $this->makeDatabase($serverId, $response->json('data'));
    }
}
