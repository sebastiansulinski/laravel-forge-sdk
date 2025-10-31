<?php

namespace SebastianSulinski\LaravelForgeSdk\Actions;

use SebastianSulinski\LaravelForgeSdk\Client;
use SebastianSulinski\LaravelForgeSdk\Data\ListResponse;
use SebastianSulinski\LaravelForgeSdk\Enums\PaginationMode;
use SebastianSulinski\LaravelForgeSdk\Payload\ListServersPayload;
use SebastianSulinski\LaravelForgeSdk\Traits\HasServer;
use SebastianSulinski\LaravelForgeSdk\Traits\ParsesResponse;

/**
 * @phpstan-import-type ServerData from HasServer
 */
readonly class ListServers
{
    use HasServer;
    use ParsesResponse;

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
     * @throws \Illuminate\Http\Client\ConnectionException
     * @throws \Illuminate\Http\Client\RequestException
     */
    public function handle(ListServersPayload $payload = new ListServersPayload): ListResponse
    {
        $path = $this->client->path('/servers');

        return match ($payload->mode) {
            PaginationMode::All => $this->fetchAll($path, $payload),
            PaginationMode::Paginated => $this->fetchSinglePage($path, $payload),
        };
    }

    /**
     * Fetch all pages.
     *
     * @throws \Illuminate\Http\Client\ConnectionException
     * @throws \Illuminate\Http\Client\RequestException
     */
    private function fetchAll(string $path, ListServersPayload $payload): ListResponse
    {
        $response = $this->fetchAllPages->handle(
            path: $path,
            query: $payload->toQuery(),
            initialCursor: $payload->pageCursor
        );

        /** @var array<int, ServerData> $data */
        $data = $response->data;

        $servers = array_map(
            fn (array $server) => $this->makeServer($server),
            $data
        );

        return new ListResponse(
            data: $servers,
            links: $response->links,
            meta: $response->meta,
            included: $response->included
        );
    }

    /**
     * Fetch a single page.
     *
     * @throws \Illuminate\Http\Client\RequestException
     * @throws \Illuminate\Http\Client\ConnectionException
     */
    private function fetchSinglePage(string $path, ListServersPayload $payload): ListResponse
    {
        $httpResponse = $this->client->get(
            path: $path,
            query: $payload->toQuery()
        )->throw();

        /** @var array<int, ServerData> $servers */
        $servers = $this->parseDataList($httpResponse);

        $mappedServers = array_map(
            fn (array $server) => $this->makeServer($server),
            $servers
        );

        return new ListResponse(
            data: $mappedServers,
            links: $this->parseLinks($httpResponse),
            meta: $this->parseMeta($httpResponse),
            included: $this->parseIncluded($httpResponse)
        );
    }
}
