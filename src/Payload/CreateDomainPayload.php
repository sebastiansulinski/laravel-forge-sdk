<?php

namespace SebastianSulinski\LaravelForgeSdk\Payload;

use Illuminate\Contracts\Support\Arrayable;
use SebastianSulinski\LaravelForgeSdk\Enums\WwwRedirectType;

/**
 * @implements Arrayable<string, mixed>
 */
readonly class CreateDomainPayload implements Arrayable
{
    /**
     * CreateDomainPayload constructor.
     */
    public function __construct(
        public string $name,
        public bool $allow_wildcard_subdomains = false,
        public WwwRedirectType $www_redirect_type = WwwRedirectType::None,
    ) {}

    /**
     * {@inheritDoc}
     */
    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'allow_wildcard_subdomains' => $this->allow_wildcard_subdomains,
            'www_redirect_type' => $this->www_redirect_type->value,
        ];
    }
}
