<?php

namespace SebastianSulinski\LaravelForgeSdk\Payload\Database;

use Illuminate\Contracts\Support\Arrayable;

/**
 * @implements Arrayable<string, mixed>
 */
readonly class CreateUserPayload implements Arrayable
{
    /**
     * CreateUserPayload constructor.
     *
     * @param  array<int>|null  $databaseIds
     */
    public function __construct(
        public string $name,
        public string $password,
        public ?bool $readOnly = null,
        public ?array $databaseIds = null,
    ) {}

    /**
     * {@inheritDoc}
     */
    public function toArray(): array
    {
        return array_filter([
            'name' => $this->name,
            'password' => $this->password,
            'read_only' => $this->readOnly,
            'database_ids' => $this->databaseIds,
        ], fn ($value) => $value !== null);
    }
}
