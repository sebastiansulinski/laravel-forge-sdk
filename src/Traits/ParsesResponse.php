<?php

namespace SebastianSulinski\LaravelForgeSdk\Traits;

use Illuminate\Http\Client\Response as HttpResponse;

trait ParsesResponse
{
    /**
     * Parse response data.
     *
     * @return array<string, mixed>
     */
    protected function parseData(HttpResponse $response): array
    {
        $data = $response->json('data');

        return is_array($data) ? $data : [];
    }

    /**
     * Parse response data as a list.
     *
     * @return array<int, array<string, mixed>>
     */
    protected function parseDataList(HttpResponse $response): array
    {
        $data = $response->json('data');

        if (! is_array($data)) {
            return [];
        }

        /** @var array<int, array<string, mixed>> $data */
        return $data;
    }

    /**
     * Parse response links.
     *
     * @return array<string, mixed>
     */
    protected function parseLinks(HttpResponse $response): array
    {
        $links = $response->json('links');

        return is_array($links) ? $links : [];
    }

    /**
     * Parse response meta.
     *
     * @return array<string, mixed>
     */
    protected function parseMeta(HttpResponse $response): array
    {
        $meta = $response->json('meta');

        return is_array($meta) ? $meta : [];
    }

    /**
     * Parse response included resources.
     *
     * @return array<int, array<string, mixed>>
     */
    protected function parseIncluded(HttpResponse $response): array
    {
        $included = $response->json('included');

        if (! is_array($included)) {
            return [];
        }

        /** @var array<int, array<string, mixed>> $included */
        return $included;
    }
}
