<?php

namespace SebastianSulinski\LaravelForgeSdk\Actions;

use SebastianSulinski\LaravelForgeSdk\Client;
use SebastianSulinski\LaravelForgeSdk\Data\Site;
use SebastianSulinski\LaravelForgeSdk\Exceptions\RequestFailed;
use SebastianSulinski\LaravelForgeSdk\Traits\HasSite;

readonly class GetSite
{
    use HasSite;

    /**
     * GetSite constructor.
     */
    public function __construct(private Client $client) {}

    /**
     * Handle request.
     *
     * @throws \SebastianSulinski\LaravelForgeSdk\Exceptions\RequestFailed
     * @throws \Illuminate\Http\Client\ConnectionException
     * @throws \Illuminate\Http\Client\RequestException
     */
    public function handle(int $siteId): Site
    {
        $path = $this->client->path(sprintf('/sites/%s', $siteId));

        $response = $this->client->get($path)->throw();

        if (! $response->successful()) {
            throw new RequestFailed(
                $response->json('message', 'Response returned: '.$response->status())
            );
        }

        return $this->makeSite($response->json('data'));
    }
}
