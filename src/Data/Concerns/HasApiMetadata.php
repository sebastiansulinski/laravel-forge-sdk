<?php

namespace SebastianSulinski\LaravelForgeSdk\Data\Concerns;

trait HasApiMetadata
{
    /**
     * Get a relationship value using dot notation.
     *
     * @param  string  $key  Dot-separated key path (e.g., 'tags.data.0.type')
     * @param  mixed  $default  Default value if key doesn't exist
     */
    public function relationships(string $key, mixed $default = null): mixed
    {
        return data_get($this->relationships, $key, $default);
    }

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
     * Check if a relationship exists.
     */
    public function hasRelationship(string $key): bool
    {
        return data_get($this->relationships, $key) !== null;
    }

    /**
     * Check if a link exists.
     */
    public function hasLink(string $key): bool
    {
        return data_get($this->links, $key) !== null;
    }
}
