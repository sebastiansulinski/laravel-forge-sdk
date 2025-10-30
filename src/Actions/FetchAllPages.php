<?php

namespace SebastianSulinski\LaravelForgeSdk\Actions;

use Illuminate\Http\Client\Response;
use SebastianSulinski\LaravelForgeSdk\Client;

/**
 * @phpstan-type QueryArray array<string, string|int|null|array<string|int|null>>
 */
readonly class FetchAllPages
{
    /**
     * FetchAllPages constructor.
     */
    public function __construct(private Client $client) {}

    /**
     * Fetch all paginated results.
     *
     * @param  QueryArray  $query
     * @return QueryArray
     *
     * @throws \Illuminate\Http\Client\ConnectionException
     * @throws \Illuminate\Http\Client\RequestException
     */
    public function handle(
        string $path,
        array $query = [],
        ?string $initialCursor = null
    ): array {
        $allResults = [];
        $cursor = $initialCursor;

        do {
            $currentQuery = $query;

            if ($cursor !== null) {
                $currentQuery['page[cursor]'] = $cursor;
            }

            $response = $this->client->get($path, $currentQuery)->throw();

            /** @var QueryArray $data */
            $data = $response->json('data', []);

            $allResults = array_merge($allResults, $data);

            /** @var string|null $cursor */
            $cursor = $response->json('meta.next_cursor');

            if ($cursor !== null) {
                $this->handleRateLimiting($response);
            }
        } while ($cursor !== null);

        return $allResults;
    }

    /**
     * Handle rate limiting between pagination requests.
     */
    private function handleRateLimiting(Response $response): void
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
