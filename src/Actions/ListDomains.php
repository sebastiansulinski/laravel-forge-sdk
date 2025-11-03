<?php

namespace SebastianSulinski\LaravelForgeSdk\Actions;

use SebastianSulinski\LaravelForgeSdk\Client;
use SebastianSulinski\LaravelForgeSdk\Data\ListResponse;
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
     * @throws \Illuminate\Http\Client\ConnectionException
     * @throws \Illuminate\Http\Client\RequestException
     */
    public function handle(int $serverId, int $siteId): ListResponse
    {
        $path = $this->client->path('/servers/%s/sites/%s/domains', $serverId, $siteId);

        $httpResponse = $this->client->get(
            path: $path
        )->throw();

        /** @var array<int, DomainData> $domains */
        $domains = $this->parseDataList($httpResponse);

        $mappedDomains = array_map(
            fn (array $domain) => $this->makeDomain($domain),
            $domains
        );

        return new ListResponse(
            data: $mappedDomains,
            links: $this->parseLinks($httpResponse),
            meta: $this->parseMeta($httpResponse),
            included: $this->parseIncluded($httpResponse)
        );
    }
}
