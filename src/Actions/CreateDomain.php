<?php

namespace SebastianSulinski\LaravelForgeSdk\Actions;

use SebastianSulinski\LaravelForgeSdk\Client;
use SebastianSulinski\LaravelForgeSdk\Data\Domain;
use SebastianSulinski\LaravelForgeSdk\Exceptions\RequestFailed;
use SebastianSulinski\LaravelForgeSdk\Payload\CreateDomainPayload;
use SebastianSulinski\LaravelForgeSdk\Traits\HasDomain;

readonly class CreateDomain
{
    use HasDomain;

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
    public function handle(int $serverId, int $siteId, CreateDomainPayload $payload): Domain
    {
        $path = $this->client->path(
            sprintf('/servers/%s/sites/%s/domains', $serverId, $siteId)
        );

        $response = $this->client->post($path, $payload->toArray())->throw();

        $data = $response->json('data', []);

        if (empty($data)) {
            throw new RequestFailed('Unable to create domain.');
        }

        return $this->makeDomain($serverId, $siteId, $data);
    }
}
