<?php

namespace SebastianSulinski\LaravelForgeSdk\Payload\Server;

use SebastianSulinski\LaravelForgeSdk\Enums\PaginationMode;
use SebastianSulinski\LaravelForgeSdk\Payload\PaginationParameters;
use SebastianSulinski\LaravelForgeSdk\Traits\BuildsListQuery;

readonly class ListPayload
{
    use BuildsListQuery;

    /**
     * ListServersPayload constructor.
     */
    public function __construct(
        public PaginationMode $mode = PaginationMode::Paginated,
        public ?string $sort = null,
        public ?int $pageSize = null,
        public ?string $pageCursor = null,
        public ?string $filterIpAddress = null,
        public ?string $filterName = null,
        public ?string $filterRegion = null,
        public ?string $filterSize = null,
        public ?string $filterProvider = null,
        public ?string $filterUbuntuVersion = null,
        public ?string $filterPhpVersion = null,
        public ?string $filterDatabaseType = null,
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
                if ($this->filterIpAddress !== null) {
                    $query['filter[ip_address]'] = $this->filterIpAddress;
                }

                if ($this->filterName !== null) {
                    $query['filter[name]'] = $this->filterName;
                }

                if ($this->filterRegion !== null) {
                    $query['filter[region]'] = $this->filterRegion;
                }

                if ($this->filterSize !== null) {
                    $query['filter[size]'] = $this->filterSize;
                }

                if ($this->filterProvider !== null) {
                    $query['filter[provider]'] = $this->filterProvider;
                }

                if ($this->filterUbuntuVersion !== null) {
                    $query['filter[ubuntu_version]'] = $this->filterUbuntuVersion;
                }

                if ($this->filterPhpVersion !== null) {
                    $query['filter[php_version]'] = $this->filterPhpVersion;
                }

                if ($this->filterDatabaseType !== null) {
                    $query['filter[database_type]'] = $this->filterDatabaseType;
                }
            }
        );
    }
}
