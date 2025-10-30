<?php

namespace SebastianSulinski\LaravelForgeSdk\Actions;

use Illuminate\Support\Collection;
use SebastianSulinski\LaravelForgeSdk\Client;
use SebastianSulinski\LaravelForgeSdk\Payload\ListDeploymentsPayload;
use SebastianSulinski\LaravelForgeSdk\Traits\HasDeployment;

readonly class ListDeployments
{
    use HasDeployment;

    /**
     * ListDeployments constructor.
     */
    public function __construct(
        private Client $client,
        private FetchAllPages $fetchAllPages,
    ) {}

    /**
     * Handle request.
     *
     * @return Collection<int, \SebastianSulinski\LaravelForgeSdk\Data\Deployment>
     *
     * @throws \Illuminate\Http\Client\ConnectionException
     * @throws \Illuminate\Http\Client\RequestException
     */
    public function handle(
        int $serverId,
        int $siteId,
        ListDeploymentsPayload $payload = new ListDeploymentsPayload,
    ): Collection {

        /** @var array<int, array<string, mixed>> $allDeployments */
        $allDeployments = $this->fetchAllPages->handle(
            path: $this->client->path(
                '/servers/%s/sites/%s/deployments', $serverId, $siteId
            ),
            query: $payload->toQuery(),
            initialCursor: $payload->pageCursor
        );

        return new Collection($allDeployments)->map(
            fn (array $deployment) => $this->makeDeployment($serverId, $siteId, $deployment)
        );
    }
}
