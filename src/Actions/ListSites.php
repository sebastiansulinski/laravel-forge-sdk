<?php

namespace SebastianSulinski\LaravelForgeSdk\Actions;

use SebastianSulinski\LaravelForgeSdk\Client;
use SebastianSulinski\LaravelForgeSdk\Data\ListResponse;
use SebastianSulinski\LaravelForgeSdk\Enums\PaginationMode;
use SebastianSulinski\LaravelForgeSdk\Payload\Site\ListPayload;
use SebastianSulinski\LaravelForgeSdk\Traits\HasSite;
use SebastianSulinski\LaravelForgeSdk\Traits\ParsesResponse;

/**
 * @phpstan-import-type SiteData from HasSite
 */
readonly class ListSites
{
    use HasSite;
    use ParsesResponse;

    /**
     * ListSites constructor.
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
        ListPayload $payload = new ListPayload
    ): ListResponse {

        $path = $this->client->path('/servers/%s/sites', $serverId);

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
    private function fetchAll(string $path, ListPayload $payload): ListResponse
    {
        $response = $this->fetchAllPages->handle(
            path: $path,
            query: $payload->toQuery(),
            initialCursor: $payload->pageCursor
        );

        /** @var array<int, SiteData> $data */
        $data = $response->data;

        $sites = array_map(
            fn (array $site) => $this->makeSite($site),
            $data
        );

        return new ListResponse(
            data: $sites,
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
    private function fetchSinglePage(string $path, ListPayload $payload): ListResponse
    {
        $httpResponse = $this->client->get(
            path: $path,
            query: $payload->toQuery()
        )->throw();

        /** @var array<int, SiteData> $sites */
        $sites = $this->parseDataList($httpResponse);

        $mappedSites = array_map(
            fn (array $site) => $this->makeSite($site),
            $sites
        );

        return new ListResponse(
            data: $mappedSites,
            links: $this->parseLinks($httpResponse),
            meta: $this->parseMeta($httpResponse),
            included: $this->parseIncluded($httpResponse)
        );
    }
}
