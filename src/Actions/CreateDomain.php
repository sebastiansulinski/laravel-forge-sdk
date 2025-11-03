<?php

namespace SebastianSulinski\LaravelForgeSdk\Actions;

use SebastianSulinski\LaravelForgeSdk\Client;
use SebastianSulinski\LaravelForgeSdk\Data\Domain;
use SebastianSulinski\LaravelForgeSdk\Exceptions\RequestFailed;
use SebastianSulinski\LaravelForgeSdk\Payload\Domain\CreatePayload;
use SebastianSulinski\LaravelForgeSdk\Traits\HasDomain;
use SebastianSulinski\LaravelForgeSdk\Traits\ParsesResponse;

/**
 * @phpstan-import-type DomainData from HasDomain
 */
readonly class CreateDomain
{
    use HasDomain;
    use ParsesResponse;

    /**
     * CreateDomain constructor.
     */
    public function __construct(private Client $client) {}

    /**
     * Handle request.
     *
     * @throws \Illuminate\Http\Client\ConnectionException
     * @throws \Illuminate\Http\Client\RequestException
     * @throws \SebastianSulinski\LaravelForgeSdk\Exceptions\RequestFailed
     * @throws \Exception
     */
    public function handle(int $serverId, int $siteId, CreatePayload $payload): Domain
    {
        $path = $this->client->path(
            '/servers/%s/sites/%s/domains', $serverId, $siteId
        );

        $response = $this->client->post($path, $payload->toArray())->throw();

        $data = $this->parseData($response);

        if (empty($data)) {
            throw new RequestFailed('Unable to create domain.');
        }

        /** @var DomainData $data */
        return $this->makeDomain($data);
    }
}
