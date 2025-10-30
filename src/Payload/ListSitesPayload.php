<?php

namespace SebastianSulinski\LaravelForgeSdk\Payload;

use SebastianSulinski\LaravelForgeSdk\Enums\SiteInclude;
use SebastianSulinski\LaravelForgeSdk\Traits\BuildsListQuery;

readonly class ListSitesPayload
{
    use BuildsListQuery;

    /**
     * ListSitesPayload constructor.
     *
     * @param  array<SiteInclude>  $include
     */
    public function __construct(
        public ?string $filterName = null,
        public ?string $sort = null,
        public ?int $pageSize = null,
        public ?string $pageCursor = null,
        public array $include = [],
    ) {}

    /**
     * Transform to a query array.
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
                        fn (SiteInclude $item) => $item->value,
                        $this->include
                    ));
                }
            }
        );
    }
}
