<?php

namespace SebastianSulinski\LaravelForgeSdk\Actions;

use SebastianSulinski\LaravelForgeSdk\Client;
use SebastianSulinski\LaravelForgeSdk\Data\ListResponse;
use SebastianSulinski\LaravelForgeSdk\Enums\PaginationMode;
use SebastianSulinski\LaravelForgeSdk\Payload\Command\ListPayload;
use SebastianSulinski\LaravelForgeSdk\Traits\HasCommand;
use SebastianSulinski\LaravelForgeSdk\Traits\ParsesResponse;

/**
 * @phpstan-import-type CommandData from HasCommand
 */
readonly class ListCommands
{
    use HasCommand;
    use ParsesResponse;

    /**
     * ListCommands constructor.
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
        int $siteId,
        ListPayload $payload = new ListPayload,
    ): ListResponse {

        $path = $this->client->path('/servers/%s/sites/%s/commands', $serverId, $siteId);

        return match ($payload->mode) {
            PaginationMode::All => $this->fetchAll(
                path: $path, siteId: $siteId, payload: $payload
            ),
            PaginationMode::Paginated => $this->fetchSinglePage(
                path: $path, siteId: $siteId, payload: $payload
            ),
        };
    }

    /**
     * Fetch all pages.
     *
     * @throws \Illuminate\Http\Client\ConnectionException
     * @throws \Illuminate\Http\Client\RequestException
     */
    private function fetchAll(string $path, int $siteId, ListPayload $payload): ListResponse
    {
        $response = $this->fetchAllPages->handle(
            path: $path,
            query: $payload->toQuery(),
            initialCursor: $payload->pageCursor
        );

        /** @var array<int, CommandData> $data */
        $data = $response->data;

        $commands = array_map(
            fn (array $command) => $this->makeCommand($siteId, $command),
            $data
        );

        return new ListResponse(
            data: $commands,
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
    private function fetchSinglePage(
        string $path,
        int $siteId,
        ListPayload $payload
    ): ListResponse {
        $httpResponse = $this->client->get(
            path: $path,
            query: $payload->toQuery()
        )->throw();

        /** @var array<int, CommandData> $commands */
        $commands = $this->parseDataList($httpResponse);

        $mappedCommands = array_map(
            fn (array $command) => $this->makeCommand($siteId, $command),
            $commands
        );

        return new ListResponse(
            data: $mappedCommands,
            links: $this->parseLinks($httpResponse),
            meta: $this->parseMeta($httpResponse),
            included: $this->parseIncluded($httpResponse)
        );
    }
}
