<?php

namespace SebastianSulinski\LaravelForgeSdk\Actions;

use SebastianSulinski\LaravelForgeSdk\Client;
use SebastianSulinski\LaravelForgeSdk\Data\ListResponse;
use SebastianSulinski\LaravelForgeSdk\Traits\HasCertificate;
use SebastianSulinski\LaravelForgeSdk\Traits\ParsesResponse;

/**
 * @phpstan-import-type CertificateData from HasCertificate
 */
readonly class ListDomainCertificates
{
    use HasCertificate;
    use ParsesResponse;

    /**
     * ListDomainCertificates constructor.
     */
    public function __construct(private Client $client) {}

    /**
     * Handle request.
     *
     * @throws \Illuminate\Http\Client\ConnectionException
     * @throws \Illuminate\Http\Client\RequestException
     */
    public function handle(int $serverId, int $siteId, int $domainRecordId): ListResponse
    {
        $path = $this->client->path(
            '/servers/%s/sites/%s/domains/%s/certificates',
            $serverId,
            $siteId,
            $domainRecordId
        );

        $httpResponse = $this->client->get(
            path: $path
        )->throw();

        /** @var array<int, CertificateData> $certificates */
        $certificates = $this->parseDataList($httpResponse);

        $mappedCertificates = array_map(
            fn (array $certificate) => $this->makeCertificate($certificate),
            $certificates
        );

        return new ListResponse(
            data: $mappedCertificates,
            links: $this->parseLinks($httpResponse),
            meta: $this->parseMeta($httpResponse),
            included: $this->parseIncluded($httpResponse)
        );
    }
}
