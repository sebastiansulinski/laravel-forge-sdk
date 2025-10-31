<?php

namespace SebastianSulinski\LaravelForgeSdk\Actions;

use Illuminate\Support\Collection;
use SebastianSulinski\LaravelForgeSdk\Client;
use SebastianSulinski\LaravelForgeSdk\Payload\ListSitesPayload;
use SebastianSulinski\LaravelForgeSdk\Traits\HasSite;

/**
 * @phpstan-import-type SiteData from HasSite
 */
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
     * @return Collection<int, \SebastianSulinski\LaravelForgeSdk\Data\Site>
     *
     * @throws \Illuminate\Http\Client\ConnectionException
     * @throws \Illuminate\Http\Client\RequestException
     */
    public function handle(
        int $serverId,
        ListSitesPayload $payload = new ListSitesPayload
    ): Collection {

        /** @var array<int, SiteData> $allSites */
        $allSites = $this->fetchAllPages->handle(
            path: $this->client->path('/servers/%s/sites', $serverId),
            query: $payload->toQuery(),
            initialCursor: $payload->pageCursor
        );

        return new Collection($allSites)->map(
            fn (array $site) => $this->makeSite($site)
        );
    }
}
