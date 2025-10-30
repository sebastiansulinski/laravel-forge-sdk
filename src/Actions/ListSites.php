<?php

namespace SebastianSulinski\LaravelForgeSdk\Actions;

use SebastianSulinski\LaravelForgeSdk\Client;
use SebastianSulinski\LaravelForgeSdk\Payload\ListSitesPayload;
use SebastianSulinski\LaravelForgeSdk\Traits\HasSite;
use Illuminate\Support\Collection;

readonly class ListSites
{
    use HasSite;

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
     * @return Collection<\SebastianSulinski\LaravelForgeSdk\Data\Site>
     *
     * @throws \Illuminate\Http\Client\ConnectionException
     * @throws \Illuminate\Http\Client\RequestException
     */
    public function handle(
        int $serverId,
        ListSitesPayload $payload = new ListSitesPayload
    ): Collection {

        $allSites = $this->fetchAllPages->handle(
            path: $this->client->path(sprintf('/servers/%s/sites', $serverId)),
            query: $payload->toQuery(),
            initialCursor: $payload->pageCursor
        );

        return new Collection($allSites)->map(
            fn (array $site) => $this->makeSite($serverId, $site)
        );
    }
}
