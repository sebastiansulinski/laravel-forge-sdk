<?php

namespace SebastianSulinski\LaravelForgeSdk\Payload\Database;

use Illuminate\Contracts\Support\Arrayable;

/**
 * @implements Arrayable<string, mixed>
 */
readonly class CreateSchemaPayload implements Arrayable
{
    /**
     * CreateDatabasePayload constructor.
     */
    public function __construct(
        public string $name,
        public ?string $user = null,
        public ?string $password = null,
    ) {}

    /**
     * {@inheritDoc}
     */
    public function toArray(): array
    {
        return array_filter([
            'name' => $this->name,
            'user' => $this->user,
            'password' => $this->password,
        ], fn ($value) => $value !== null);
    }
}
