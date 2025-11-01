<?php

namespace SebastianSulinski\LaravelForgeSdk\Payload\Deployment;

use SebastianSulinski\LaravelForgeSdk\Enums\PaginationMode;
use SebastianSulinski\LaravelForgeSdk\Payload\PaginationParameters;
use SebastianSulinski\LaravelForgeSdk\Traits\BuildsListQuery;

readonly class ListPayload
{
    use BuildsListQuery;

    /**
     * ListDeploymentsPayload constructor.
     */
    public function __construct(
        public PaginationMode $mode = PaginationMode::Paginated,
        public ?string $sort = null,
        public ?int $pageSize = null,
        public ?string $pageCursor = null,
        public ?string $filterCommitHash = null,
        public ?string $filterCommitMessage = null,
        public ?string $filterCommitAuthor = null,
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
                if ($this->filterCommitHash !== null) {
                    $query['filter[commit_hash]'] = $this->filterCommitHash;
                }

                if ($this->filterCommitMessage !== null) {
                    $query['filter[commit_message]'] = $this->filterCommitMessage;
                }

                if ($this->filterCommitAuthor !== null) {
                    $query['filter[commit_author]'] = $this->filterCommitAuthor;
                }
            }
        );
    }
}
