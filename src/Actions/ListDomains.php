<?php

namespace SebastianSulinski\LaravelForgeSdk\Actions;

use Illuminate\Http\Client\Response;
use Illuminate\Support\Collection;
use SebastianSulinski\LaravelForgeSdk\Client;
use SebastianSulinski\LaravelForgeSdk\Traits\HasDomain;

/**
 * @phpstan-type DomainData array{
 *     id: int,
 *     attributes: array{
 *         name: string,
 *         type: string,
 *         status: string,
 *         www_redirect_type: string,
 *         allow_wildcard_subdomains: bool,
 *         created_at: string,
 *         updated_at: string
 *     }
 * }
 * @phpstan-type DataArray array<int, DomainData>
 */
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

        return new Collection($this->responseData($response))->map(
            fn (array $domain) => $this->makeDomain($serverId, $siteId, $domain)
        );
    }

    /**
     * Get the response data.
     *
     * @return DataArray
     */
    private function responseData(Response $response): array
    {
        return $response->json('data', []);
    }
}
