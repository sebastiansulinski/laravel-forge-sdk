<?php

namespace SebastianSulinski\LaravelForgeSdk\Actions;

use Illuminate\Http\Client\Response as HttpResponse;
use SebastianSulinski\LaravelForgeSdk\Client;
use SebastianSulinski\LaravelForgeSdk\Data\ListResponse;
use SebastianSulinski\LaravelForgeSdk\Traits\ParsesResponse;

/**
 * @phpstan-type QueryArray array<string, string|int|null|array<string|int|null>>
 */
readonly class FetchAllPages
{
    use ParsesResponse;

    /**
     * FetchAllPages constructor.
     */
    public function __construct(private Client $client) {}

    /**
     * Fetch all paginated results.
     *
     * @param  QueryArray  $query
     *
     * @throws \Illuminate\Http\Client\ConnectionException
     * @throws \Illuminate\Http\Client\RequestException
     */
    public function handle(
        string $path,
        array $query = [],
        ?string $initialCursor = null
    ): ListResponse {
        $allResults = [];
        $cursor = $initialCursor;

        do {
            $currentQuery = $query;

            if ($cursor !== null) {
                $currentQuery['page[cursor]'] = $cursor;
            }

            $lastResponse = $this->client->get($path, $currentQuery)->throw();

            $allResults = array_merge($allResults, $this->parseDataList($lastResponse));

            /** @var string|null $cursor */
            $cursor = $this->parseMeta($lastResponse)['next_cursor'] ?? null;

            if ($cursor !== null) {
                $this->handleRateLimiting($lastResponse);
            }
        } while ($cursor !== null);

        return new ListResponse(
            data: $allResults,
            links: $this->parseLinks($lastResponse),
            meta: $this->parseMeta($lastResponse),
            included: $this->parseIncluded($lastResponse)
        );
    }

    /**
     * Handle rate limiting between pagination requests.
     */
    private function handleRateLimiting(HttpResponse $response): void
    {
        $remaining = (int) $response->header('X-RateLimit-Remaining');
        $resetTime = (int) $response->header('X-RateLimit-Reset');

        if ($remaining <= 5 && $resetTime > 0) {
            $waitTime = max(1, $resetTime - time());
            sleep($waitTime);
        } elseif ($remaining <= 10) {
            usleep(100000);
        }
    }
}
