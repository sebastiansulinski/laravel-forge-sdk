<?php

namespace SebastianSulinski\LaravelForgeSdk\Actions;

use Illuminate\Support\Collection;
use SebastianSulinski\LaravelForgeSdk\Client;
use SebastianSulinski\LaravelForgeSdk\Payload\ListDatabaseUsersPayload;
use SebastianSulinski\LaravelForgeSdk\Traits\HasDatabaseUser;

/**
 * @phpstan-import-type DatabaseUserData from HasDatabaseUser
 */
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
     * @return Collection<int, \SebastianSulinski\LaravelForgeSdk\Data\DatabaseUser>
     *
     * @throws \Illuminate\Http\Client\ConnectionException
     * @throws \Illuminate\Http\Client\RequestException
     */
    public function handle(
        int $serverId,
        ListDatabaseUsersPayload $payload = new ListDatabaseUsersPayload
    ): Collection {

        /** @var array<int, DatabaseUserData> $allUsers */
        $allUsers = $this->fetchAllPages->handle(
            path: $this->client->path(
                '/servers/%s/database/users', $serverId
            ),
            query: $payload->toQuery(),
            initialCursor: $payload->pageCursor
        );

        return new Collection($allUsers)->map(
            fn (array $user) => $this->makeDatabaseUser($serverId, $user)
        );
    }
}
