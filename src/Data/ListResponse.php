<?php

namespace SebastianSulinski\LaravelForgeSdk\Data;

use Illuminate\Support\Collection;

readonly class ListResponse
{
    /**
     * ListResponse constructor.
     *
     * @param  array<int, mixed>  $data
     * @param  array<string, mixed>  $links
     * @param  array<string, mixed>  $meta
     * @param  array<int, array<string, mixed>>  $included
     */
    public function __construct(
        public array $data,
        public array $links = [],
        public array $meta = [],
        public array $included = [],
    ) {}

    /**
     * Check if a response has data.
     */
    public function hasData(): bool
    {
        return ! empty($this->data);
    }

    /**
     * Check if a response has links.
     */
    public function hasLinks(): bool
    {
        return ! empty($this->links);
    }

    /**
     * Check if a response has metadata.
     */
    public function hasMeta(): bool
    {
        return ! empty($this->meta);
    }

    /**
     * Check if a response has included resources.
     */
    public function hasIncluded(): bool
    {
        return ! empty($this->included);
    }

    /**
     * Get a specific link value.
     */
    public function link(string $key, mixed $default = null): mixed
    {
        return data_get($this->links, $key, $default);
    }

    /**
     * Get a specific meta value.
     */
    public function meta(string $key, mixed $default = null): mixed
    {
        return data_get($this->meta, $key, $default);
    }

    /**
     * Get data as a collection.
     *
     * @return Collection<int, mixed>
     */
    public function collection(): Collection
    {
        return new Collection($this->data);
    }

    /**
     * Get included resources as a collection.
     *
     * @return Collection<int, array<string, mixed>>
     */
    public function included(): Collection
    {
        return new Collection($this->included);
    }
}
