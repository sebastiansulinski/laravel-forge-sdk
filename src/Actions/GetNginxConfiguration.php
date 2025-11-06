<?php

namespace SebastianSulinski\LaravelForgeSdk\Actions;

use Illuminate\Http\Client\Response;
use SebastianSulinski\LaravelForgeSdk\Client;
use SebastianSulinski\LaravelForgeSdk\Data\NginxConfiguration;
use SebastianSulinski\LaravelForgeSdk\Exceptions\RequestFailed;
use SebastianSulinski\LaravelForgeSdk\Traits\HasNginxConfiguration;
use SebastianSulinski\LaravelForgeSdk\Traits\ParsesResponse;

/**
 * @phpstan-import-type NginxConfigurationData from HasNginxConfiguration
 */
readonly class GetNginxConfiguration
{
    use HasNginxConfiguration;
    use ParsesResponse;

    /**
     * GetNginxConfiguration constructor.
     */
    public function __construct(private Client $client) {}

    /**
     * Handle request.
     *
     * @throws \SebastianSulinski\LaravelForgeSdk\Exceptions\RequestFailed
     * @throws \Illuminate\Http\Client\ConnectionException
     * @throws \Illuminate\Http\Client\RequestException
     */
    public function handle(int $serverId, int $siteId): NginxConfiguration
    {
        $path = $this->client->path(
            '/servers/%s/sites/%s/nginx',
            $serverId,
            $siteId
        );

        $response = $this->client->get($path)->throw();

        if (! $response->successful()) {
            throw new RequestFailed($this->exceptionMessage($response));
        }

        /** @var NginxConfigurationData $data */
        $data = $this->parseData($response);

        return $this->makeNginxConfiguration($data);
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
