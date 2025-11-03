<?php

namespace SebastianSulinski\LaravelForgeSdk\Actions;

use SebastianSulinski\LaravelForgeSdk\Client;
use SebastianSulinski\LaravelForgeSdk\Data\Domain;
use SebastianSulinski\LaravelForgeSdk\Exceptions\RequestFailed;
use SebastianSulinski\LaravelForgeSdk\Traits\HasDomain;
use SebastianSulinski\LaravelForgeSdk\Traits\ParsesResponse;

/**
 * @phpstan-import-type DomainData from HasDomain
 */
readonly class GetDomain
{
    use HasDomain;
    use ParsesResponse;

    /**
     * GetDomain constructor.
     */
    public function __construct(private Client $client) {}

    /**
     * Handle request.
     *
     * @throws \Illuminate\Http\Client\ConnectionException
     * @throws \Illuminate\Http\Client\RequestException
     * @throws \SebastianSulinski\LaravelForgeSdk\Exceptions\RequestFailed
     */
    public function handle(int $serverId, int $siteId, int $domainRecordId): Domain
    {
        $path = $this->client->path(
            '/servers/%s/sites/%s/domains/%s',
            $serverId,
            $siteId,
            $domainRecordId
        );

        $response = $this->client->get($path)->throw();

        $data = $this->parseData($response);

        if (empty($data)) {
            throw new RequestFailed('Unable to get domain.');
        }

        /** @var DomainData $data */
        return $this->makeDomain($data);
    }
}
