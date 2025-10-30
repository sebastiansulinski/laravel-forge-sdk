<?php

namespace SebastianSulinski\LaravelForgeSdk\Actions;

use Illuminate\Http\Client\Response;
use SebastianSulinski\LaravelForgeSdk\Client;
use SebastianSulinski\LaravelForgeSdk\Data\Certificate;
use SebastianSulinski\LaravelForgeSdk\Exceptions\RequestFailed;
use SebastianSulinski\LaravelForgeSdk\Payload\CreateCertificatePayload;
use SebastianSulinski\LaravelForgeSdk\Traits\HasCertificate;

readonly class CreateDomainCertificate
{
    use HasCertificate;

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
        CreateCertificatePayload $payload
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

        return $this->makeCertificate(
            serverId: $serverId,
            siteId: $siteId,
            domainRecordId: $domainRecordId,
            data: $this->responseData($response)
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

    /**
     * Get the response data.
     *
     * @return array<string, mixed>
     */
    private function responseData(Response $response): array
    {
        $data = $response->json('data');

        return is_array($data) ? $data : [];
    }
}
