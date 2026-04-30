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
    public function handle(
        int $serverId,
        int $siteId,
        int $domainRecordId,
        int $certificateId
    ): bool {
        $path = $this->client->path(
            '/servers/%s/sites/%s/domains/%s/certificates/%s',
            $serverId,
            $siteId,
            $domainRecordId,
            $certificateId
        );

        $response = $this->client->delete($path)->throw();

        return $response->status() === 202;
    }
}
