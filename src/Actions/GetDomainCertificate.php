<?php

namespace SebastianSulinski\LaravelForgeSdk\Actions;

use SebastianSulinski\LaravelForgeSdk\Client;
use SebastianSulinski\LaravelForgeSdk\Data\Certificate;
use SebastianSulinski\LaravelForgeSdk\Exceptions\RequestFailed;
use SebastianSulinski\LaravelForgeSdk\Traits\HasCertificate;
use SebastianSulinski\LaravelForgeSdk\Traits\ParsesResponse;

/**
 * @phpstan-import-type CertificateData from HasCertificate
 */
readonly class GetDomainCertificate
{
    use HasCertificate;
    use ParsesResponse;

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
            '/servers/%s/sites/%s/domains/%s/certificate',
            $serverId,
            $siteId,
            $domainRecordId
        );

        $response = $this->client->get($path)->throw();

        $data = $this->parseData($response);

        if (empty($data)) {
            throw new RequestFailed('Unable to get domain certificate.');
        }

        /** @var CertificateData $data */
        return $this->makeCertificate($data);
    }
}
