<?php

namespace SebastianSulinski\LaravelForgeSdk\Payload;

use SebastianSulinski\LaravelForgeSdk\Enums\PaginationMode;
use SebastianSulinski\LaravelForgeSdk\Traits\BuildsListQuery;

readonly class ListDatabaseUsersPayload
{
    use BuildsListQuery;

    /**
     * ListDatabaseUsersPayload constructor.
     */
    public function __construct(
        public PaginationMode $mode = PaginationMode::Paginated,
        public ?string $sort = null,
        public ?int $pageSize = null,
        public ?string $pageCursor = null,
        public ?string $filterName = null,
        public ?string $filterStatus = null,
    ) {}

    /**
     * Transform to a query array.
     *
     * @return array<string, string|int|null|array<string|int|null>>
     */
    public function toQuery(): array
    {
        return $this->buildQuery(
            new PaginationParameters($this->sort, $this->pageSize, $this->pageCursor),
            function (array &$query) {
                if ($this->filterName !== null) {
                    $query['filter[name]'] = $this->filterName;
                }

                if ($this->filterStatus !== null) {
                    $query['filter[status]'] = $this->filterStatus;
                }
            }
        );
    }
}
