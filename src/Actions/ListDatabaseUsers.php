<?php

namespace SebastianSulinski\LaravelForgeSdk\Actions;

use SebastianSulinski\LaravelForgeSdk\Client;
use SebastianSulinski\LaravelForgeSdk\Data\ListResponse;
use SebastianSulinski\LaravelForgeSdk\Enums\PaginationMode;
use SebastianSulinski\LaravelForgeSdk\Payload\Database\ListUsersPayload;
use SebastianSulinski\LaravelForgeSdk\Traits\HasDatabaseUser;
use SebastianSulinski\LaravelForgeSdk\Traits\ParsesResponse;

/**
 * @phpstan-import-type DatabaseUserData from HasDatabaseUser
 */
readonly class ListDatabaseUsers
{
    use HasDatabaseUser;
    use ParsesResponse;

    /**
     * ListDatabaseUsers constructor.
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
        ListUsersPayload $payload = new ListUsersPayload
    ): ListResponse {

        $path = $this->client->path('/servers/%s/database/users', $serverId);

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
    private function fetchAll(string $path, int $serverId, ListUsersPayload $payload): ListResponse
    {
        $response = $this->fetchAllPages->handle(
            path: $path,
            query: $payload->toQuery(),
            initialCursor: $payload->pageCursor
        );

        /** @var array<int, DatabaseUserData> $data */
        $data = $response->data;

        $users = array_map(
            fn (array $user) => $this->makeDatabaseUser($serverId, $user),
            $data
        );

        return new ListResponse(
            data: $users,
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
    private function fetchSinglePage(string $path, int $serverId, ListUsersPayload $payload): ListResponse
    {
        $httpResponse = $this->client->get(
            path: $path,
            query: $payload->toQuery()
        )->throw();

        /** @var array<int, DatabaseUserData> $users */
        $users = $this->parseDataList($httpResponse);

        $mappedUsers = array_map(
            fn (array $user) => $this->makeDatabaseUser($serverId, $user),
            $users
        );

        return new ListResponse(
            data: $mappedUsers,
            links: $this->parseLinks($httpResponse),
            meta: $this->parseMeta($httpResponse),
            included: $this->parseIncluded($httpResponse)
        );
    }
}
