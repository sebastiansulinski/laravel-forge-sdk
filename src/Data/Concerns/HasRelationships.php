<?php

namespace SebastianSulinski\LaravelForgeSdk\Data\Concerns;

trait HasRelationships
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
     * Check if a relationship exists.
     */
    public function hasRelationship(string $key): bool
    {
        return data_get($this->relationships, $key) !== null;
    }

    /**
     * Check if response has any relationships.
     */
    public function hasRelationships(): bool
    {
        return ! empty($this->relationships);
    }
}
