<?php

namespace SebastianSulinski\LaravelForgeSdk\Traits;

use SebastianSulinski\LaravelForgeSdk\Payload\PaginationParameters;

trait BuildsListQuery
{
    /**
     * Build query array with pagination and sorting.
     *
     * @return array<string, mixed>
     */
    protected function buildQuery(PaginationParameters $parameters, callable $callback): array
    {
        $query = [];

        if ($parameters->sort !== null) {
            $query['sort'] = $parameters->sort;
        }

        if ($parameters->pageSize !== null) {
            $query['page[size]'] = $parameters->pageSize;
        }

        if ($parameters->pageCursor !== null) {
            $query['page[cursor]'] = $parameters->pageCursor;
        }

        $callback($query);

        return $query;
    }
}
