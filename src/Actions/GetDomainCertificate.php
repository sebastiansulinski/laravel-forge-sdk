<?php

namespace SebastianSulinski\LaravelForgeSdk\Actions;

use SebastianSulinski\LaravelForgeSdk\Client;
use SebastianSulinski\LaravelForgeSdk\Data\Certificate;
use SebastianSulinski\LaravelForgeSdk\Exceptions\RequestFailed;
use SebastianSulinski\LaravelForgeSdk\Traits\HasCertificate;

readonly class GetDomainCertificate
{
    use HasCertificate;

    /**
     * GetDomainCertificate constructor.
     */
    public function __construct(private Client $client) {}

    /**
     * Handle request.
     *
     * @throws \Illuminate\Http\Client\ConnectionException
     * @throws \Illuminate\Http\Client\RequestException
     * @throws \SebastianSulinski\LaravelForgeSdk\Exceptions\RequestFailed
     */
    public function handle(int $serverId, int $siteId, int $domainRecordId): Certificate
    {
        $path = $this->client->path(
            sprintf('/servers/%s/sites/%s/domains/%s/certificate', $serverId, $siteId, $domainRecordId)
        );

        $response = $this->client->get($path)->throw();

        $data = $response->json('data', []);

        if (empty($data)) {
            throw new RequestFailed('Unable to get domain certificate.');
        }

        return $this->makeCertificate($serverId, $siteId, $domainRecordId, $data);
    }
}
