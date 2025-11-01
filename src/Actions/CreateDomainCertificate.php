<?php

namespace SebastianSulinski\LaravelForgeSdk\Actions;

use Illuminate\Http\Client\Response;
use SebastianSulinski\LaravelForgeSdk\Client;
use SebastianSulinski\LaravelForgeSdk\Data\Certificate;
use SebastianSulinski\LaravelForgeSdk\Exceptions\RequestFailed;
use SebastianSulinski\LaravelForgeSdk\Payload\Certificate\CreatePayload;
use SebastianSulinski\LaravelForgeSdk\Traits\HasCertificate;
use SebastianSulinski\LaravelForgeSdk\Traits\ParsesResponse;

/**
 * @phpstan-import-type CertificateData from HasCertificate
 */
readonly class CreateDomainCertificate
{
    use HasCertificate;
    use ParsesResponse;

    /**
     * CreateDomainCertificate constructor.
     */
    public function __construct(private Client $client) {}

    /**
     * Handle request.
     *
     * @throws \SebastianSulinski\LaravelForgeSdk\Exceptions\RequestFailed
     * @throws \Illuminate\Http\Client\ConnectionException
     * @throws \Illuminate\Http\Client\RequestException
     * @throws \Exception
     */
    public function handle(
        int $serverId,
        int $siteId,
        int $domainRecordId,
        CreatePayload $payload
    ): Certificate {

        $path = $this->client->path(
            '/servers/%s/sites/%s/domains/%s/certificate',
            $serverId,
            $siteId,
            $domainRecordId
        );

        $response = $this->client->post($path, $payload->toArray())->throw();

        if (! $response->successful()) {
            throw new RequestFailed($this->exceptionMessage($response));
        }

        /** @var CertificateData $data */
        $data = $this->parseData($response);

        return $this->makeCertificate(
            serverId: $serverId,
            siteId: $siteId,
            domainRecordId: $domainRecordId,
            data: $data
        );
    }

    /**
     * Get the exception message.
     */
    private function exceptionMessage(Response $response): string
    {
        $message = $response->json('message', 'Response returned: '.$response->status());

        return is_string($message) ? $message : 'Response returned: '.$response->status();
    }
}
