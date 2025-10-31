<?php

namespace SebastianSulinski\LaravelForgeSdk\Actions;

use Illuminate\Http\Client\Response;
use SebastianSulinski\LaravelForgeSdk\Client;
use SebastianSulinski\LaravelForgeSdk\Data\Site;
use SebastianSulinski\LaravelForgeSdk\Exceptions\RequestFailed;
use SebastianSulinski\LaravelForgeSdk\Payload\CreateSitePayload;
use SebastianSulinski\LaravelForgeSdk\Traits\HasSite;
use SebastianSulinski\LaravelForgeSdk\Traits\ParsesResponse;

/**
 * @phpstan-import-type SiteData from HasSite
 */
readonly class CreateSite
{
    use HasSite;
    use ParsesResponse;

    /**
     * CreateSite constructor.
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
    public function handle(int $serverId, CreateSitePayload $payload): Site
    {
        $path = $this->client->path('/servers/%s/sites', $serverId);

        $response = $this->client->post($path, $payload->toArray())->throw();

        if (! $response->successful()) {
            throw new RequestFailed($this->exceptionMessage($response));
        }

        /** @var SiteData $data */
        $data = $this->parseData($response);

        return $this->makeSite($data);
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
