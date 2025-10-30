<?php

namespace SebastianSulinski\LaravelForgeSdk\Actions;

use SebastianSulinski\LaravelForgeSdk\Client;
use SebastianSulinski\LaravelForgeSdk\Payload\ListDatabaseUsersPayload;
use SebastianSulinski\LaravelForgeSdk\Traits\HasDatabaseUser;
use Illuminate\Support\Collection;

readonly class ListDatabaseUsers
{
    use HasDatabaseUser;

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
     * @return Collection<\SebastianSulinski\LaravelForgeSdk\Data\DatabaseUser>
     *
     * @throws \Illuminate\Http\Client\ConnectionException
     * @throws \Illuminate\Http\Client\RequestException
     */
    public function handle(
        int $serverId,
        ListDatabaseUsersPayload $payload = new ListDatabaseUsersPayload
    ): Collection {

        $allUsers = $this->fetchAllPages->handle(
            path: $this->client->path(
                sprintf('/servers/%s/database/users', $serverId)
            ),
            query: $payload->toQuery(),
            initialCursor: $payload->pageCursor
        );

        return new Collection($allUsers)->map(
            fn (array $user) => $this->makeDatabaseUser($serverId, $user)
        );
    }
}
