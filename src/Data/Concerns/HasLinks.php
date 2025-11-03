<?php

namespace SebastianSulinski\LaravelForgeSdk\Data\Concerns;

trait HasLinks
{
    /**
     * Get a link value using dot notation.
     *
     * @param  string  $key  Dot-separated key path (e.g., 'self.href')
     * @param  mixed  $default  Default value if key doesn't exist
     */
    public function links(string $key, mixed $default = null): mixed
    {
        return data_get($this->links, $key, $default);
    }

    /**
     * Check if a link exists.
     */
    public function hasLink(string $key): bool
    {
        return data_get($this->links, $key) !== null;
    }

    /**
     * Check if response has any links.
     */
    public function hasLinks(): bool
    {
        return ! empty($this->links);
    }
}
