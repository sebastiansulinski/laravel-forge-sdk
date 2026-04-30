<?php

namespace SebastianSulinski\LaravelForgeSdk\Actions;

use SebastianSulinski\LaravelForgeSdk\Client;
use SebastianSulinski\LaravelForgeSdk\Payload\Certificate\CreateActionPayload;

readonly class CreateDomainCertificateAction
{
    /**
     * CreateDomainCertificateAction constructor.
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
        int $certificateId,
        CreateActionPayload $payload
    ): bool {

        $path = $this->client->path(
            '/servers/%s/sites/%s/domains/%s/certificates/%s/actions',
            $serverId,
            $siteId,
            $domainRecordId,
            $certificateId
        );

        $response = $this->client->post($path, $payload->toArray())->throw();

        return $response->successful();
    }
}
