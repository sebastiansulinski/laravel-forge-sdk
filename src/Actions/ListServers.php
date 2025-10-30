<?php

namespace SebastianSulinski\LaravelForgeSdk\Actions;

use Illuminate\Support\Collection;
use SebastianSulinski\LaravelForgeSdk\Client;
use SebastianSulinski\LaravelForgeSdk\Payload\ListServersPayload;
use SebastianSulinski\LaravelForgeSdk\Traits\HasServer;

readonly class ListServers
{
    use HasServer;

    /**
     * ListServers constructor.
     */
    public function __construct(
        private Client $client,
        private FetchAllPages $fetchAllPages,
    ) {}

    /**
     * Handle request.
     *
     * @return Collection<int, \SebastianSulinski\LaravelForgeSdk\Data\Server>
     *
     * @throws \Illuminate\Http\Client\ConnectionException
     * @throws \Illuminate\Http\Client\RequestException
     */
    public function handle(ListServersPayload $payload = new ListServersPayload): Collection
    {
        $allServers = $this->fetchAllPages->handle(
            path: $this->client->path('/servers'),
            query: $payload->toQuery(),
            initialCursor: $payload->pageCursor
        );

        /** @var array<int, array<string, mixed>> $allServers */
        return new Collection($allServers)->map(
            fn (array $server) => $this->makeServer($server)
        );
    }
}
