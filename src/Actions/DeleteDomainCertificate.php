<?php

namespace SebastianSulinski\LaravelForgeSdk\Actions;

use SebastianSulinski\LaravelForgeSdk\Client;

readonly class DeleteDomainCertificate
{
    /**
     * DeleteDomainCertificate constructor.
     */
    public function __construct(private Client $client) {}

    /**
     * Handle request.
     *
     * @throws \Illuminate\Http\Client\ConnectionException
     * @throws \Illuminate\Http\Client\RequestException
     */
    public function handle(int $serverId, int $siteId, int $domainRecordId): bool
    {
        $path = $this->client->path(
            '/servers/%s/sites/%s/domains/%s/certificate',
            $serverId,
            $siteId,
            $domainRecordId
        );

        $response = $this->client->delete($path)->throw();

        return $response->status() === 202;
    }
}
