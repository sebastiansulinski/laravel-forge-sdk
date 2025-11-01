<?php

namespace SebastianSulinski\LaravelForgeSdk\Payload\Site;

use SebastianSulinski\LaravelForgeSdk\Enums\PaginationMode;
use SebastianSulinski\LaravelForgeSdk\Enums\Site\IncludeOption;
use SebastianSulinski\LaravelForgeSdk\Payload\PaginationParameters;
use SebastianSulinski\LaravelForgeSdk\Traits\BuildsListQuery;

readonly class ListPayload
{
    use BuildsListQuery;

    /**
     * ListPayload constructor.
     *
     * @param  array<IncludeOption>  $include
     */
    public function __construct(
        public PaginationMode $mode = PaginationMode::Paginated,
        public ?string $filterName = null,
        public ?string $sort = null,
        public ?int $pageSize = null,
        public ?string $pageCursor = null,
        public array $include = [],
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

                if (! empty($this->include)) {
                    $query['include'] = implode(',', array_map(
                        fn (IncludeOption $item) => $item->value,
                        $this->include
                    ));
                }
            }
        );
    }
}
