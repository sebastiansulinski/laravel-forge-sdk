<?php

namespace SebastianSulinski\LaravelForgeSdk\Actions;

use SebastianSulinski\LaravelForgeSdk\Client;
use SebastianSulinski\LaravelForgeSdk\Data\ListResponse;
use SebastianSulinski\LaravelForgeSdk\Enums\PaginationMode;
use SebastianSulinski\LaravelForgeSdk\Payload\Deployment\ListPayload;
use SebastianSulinski\LaravelForgeSdk\Traits\HasDeployment;
use SebastianSulinski\LaravelForgeSdk\Traits\ParsesResponse;

/**
 * @phpstan-import-type DeploymentData from HasDeployment
 */
readonly class ListDeployments
{
    use HasDeployment;
    use ParsesResponse;

    /**
     * ListDeployments constructor.
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

        $path = $this->client->path('/servers/%s/sites/%s/deployments', $serverId, $siteId);

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

        /** @var array<int, DeploymentData> $data */
        $data = $response->data;

        $deployments = array_map(
            fn (array $deployment) => $this->makeDeployment($siteId, $deployment),
            $data
        );

        return new ListResponse(
            data: $deployments,
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

        /** @var array<int, DeploymentData> $deployments */
        $deployments = $this->parseDataList($httpResponse);

        $mappedDeployments = array_map(
            fn (array $deployment) => $this->makeDeployment($siteId, $deployment),
            $deployments
        );

        return new ListResponse(
            data: $mappedDeployments,
            links: $this->parseLinks($httpResponse),
            meta: $this->parseMeta($httpResponse),
            included: $this->parseIncluded($httpResponse)
        );
    }
}
