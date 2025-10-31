<?php

namespace SebastianSulinski\LaravelForgeSdk\Actions;

use Illuminate\Support\Collection;
use SebastianSulinski\LaravelForgeSdk\Client;
use SebastianSulinski\LaravelForgeSdk\Traits\HasDomain;
use SebastianSulinski\LaravelForgeSdk\Traits\ParsesResponse;

/**
 * @phpstan-import-type DomainData from HasDomain
 */
readonly class ListDomains
{
    use HasDomain;
    use ParsesResponse;

    /**
     * ListDomains constructor.
     */
    public function __construct(private Client $client) {}

    /**
     * Handle request.
     *
     * @return Collection<int, \SebastianSulinski\LaravelForgeSdk\Data\Domain>
     *
     * @throws \Illuminate\Http\Client\ConnectionException
     * @throws \Illuminate\Http\Client\RequestException
     */
    public function handle(int $serverId, int $siteId): Collection
    {
        $path = $this->client->path(
            '/servers/%s/sites/%s/domains', $serverId, $siteId
        );

        $response = $this->client->get($path)->throw();

        /** @var array<int, DomainData> $domains */
        $domains = $this->parseDataList($response);

        return new Collection($domains)->map(
            fn (array $domain) => $this->makeDomain($serverId, $siteId, $domain)
        );
    }
}
