<?php

namespace SebastianSulinski\LaravelForgeSdk\Payload\Command;

use SebastianSulinski\LaravelForgeSdk\Enums\PaginationMode;
use SebastianSulinski\LaravelForgeSdk\Payload\PaginationParameters;
use SebastianSulinski\LaravelForgeSdk\Traits\BuildsListQuery;

readonly class ListPayload
{
    use BuildsListQuery;

    /**
     * ListPayload constructor.
     */
    public function __construct(
        public PaginationMode $mode = PaginationMode::Paginated,
        public ?string $sort = null,
        public ?int $pageSize = null,
        public ?string $pageCursor = null,
        public ?int $filterUserId = null,
        public ?string $filterStatus = null,
        public ?string $filterCommand = null,
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
                if ($this->filterUserId !== null) {
                    $query['filter[user_id]'] = $this->filterUserId;
                }

                if ($this->filterStatus !== null) {
                    $query['filter[status]'] = $this->filterStatus;
                }

                if ($this->filterCommand !== null) {
                    $query['filter[command]'] = $this->filterCommand;
                }
            }
        );
    }
}
