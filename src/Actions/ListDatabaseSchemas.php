<?php

namespace SebastianSulinski\LaravelForgeSdk\Actions;

use SebastianSulinski\LaravelForgeSdk\Client;
use SebastianSulinski\LaravelForgeSdk\Data\ListResponse;
use SebastianSulinski\LaravelForgeSdk\Enums\PaginationMode;
use SebastianSulinski\LaravelForgeSdk\Payload\ListDatabaseSchemasPayload;
use SebastianSulinski\LaravelForgeSdk\Traits\HasDatabase;
use SebastianSulinski\LaravelForgeSdk\Traits\ParsesResponse;

/**
 * @phpstan-import-type DatabaseData from HasDatabase
 */
readonly class ListDatabaseSchemas
{
    use HasDatabase;
    use ParsesResponse;

    /**
     * ListDatabaseSchemas constructor.
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
    public function handle(
        int $serverId,
        ListDatabaseSchemasPayload $payload = new ListDatabaseSchemasPayload
    ): ListResponse {

        $path = $this->client->path('/servers/%s/database/schemas', $serverId);

        return match ($payload->mode) {
            PaginationMode::All => $this->fetchAll($path, $serverId, $payload),
            PaginationMode::Paginated => $this->fetchSinglePage($path, $serverId, $payload),
        };
    }

    /**
     * Fetch all pages.
     *
     * @throws \Illuminate\Http\Client\ConnectionException
     * @throws \Illuminate\Http\Client\RequestException
     */
    private function fetchAll(string $path, int $serverId, ListDatabaseSchemasPayload $payload): ListResponse
    {
        $response = $this->fetchAllPages->handle(
            path: $path,
            query: $payload->toQuery(),
            initialCursor: $payload->pageCursor
        );

        /** @var array<int, DatabaseData> $data */
        $data = $response->data;

        $schemas = array_map(
            fn (array $schema) => $this->makeDatabase($serverId, $schema),
            $data
        );

        return new ListResponse(
            data: $schemas,
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
    private function fetchSinglePage(string $path, int $serverId, ListDatabaseSchemasPayload $payload): ListResponse
    {
        $httpResponse = $this->client->get(
            path: $path,
            query: $payload->toQuery()
        )->throw();

        /** @var array<int, DatabaseData> $schemas */
        $schemas = $this->parseDataList($httpResponse);

        $mappedSchemas = array_map(
            fn (array $schema) => $this->makeDatabase($serverId, $schema),
            $schemas
        );

        return new ListResponse(
            data: $mappedSchemas,
            links: $this->parseLinks($httpResponse),
            meta: $this->parseMeta($httpResponse),
            included: $this->parseIncluded($httpResponse)
        );
    }
}
