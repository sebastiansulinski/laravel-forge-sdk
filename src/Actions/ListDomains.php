<?php

namespace SebastianSulinski\LaravelForgeSdk\Actions;

use SebastianSulinski\LaravelForgeSdk\Client;
use SebastianSulinski\LaravelForgeSdk\Traits\HasDomain;
use Illuminate\Support\Collection;

readonly class ListDomains
{
    use HasDomain;

    /**
     * ListDomains constructor.
     */
    public function __construct(private Client $client) {}

    /**
     * Handle request.
     *
     * @return Collection<\SebastianSulinski\LaravelForgeSdk\Data\Domain>
     *
     * @throws \Illuminate\Http\Client\ConnectionException
     * @throws \Illuminate\Http\Client\RequestException
     */
    public function handle(int $serverId, int $siteId): Collection
    {
        $path = $this->client->path(
            sprintf('/servers/%s/sites/%s/domains', $serverId, $siteId)
        );

        $response = $this->client->get($path)->throw();

        return new Collection($response->json('data', []))->map(
            fn (array $domain) => $this->makeDomain($serverId, $siteId, $domain)
        );
    }
}
