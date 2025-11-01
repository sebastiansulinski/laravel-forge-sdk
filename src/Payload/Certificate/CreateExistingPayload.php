<?php

namespace SebastianSulinski\LaravelForgeSdk\Payload\Certificate;

use SebastianSulinski\LaravelForgeSdk\Enums\Certificate\Type;

readonly class CreateExistingPayload extends CreatePayload
{
    /**
     * CreateExistingCertificatePayload constructor.
     */
    public function __construct(
        public string $key,
        public string $certificate,
    ) {}

    /**
     * {@inheritDoc}
     */
    public function toArray(): array
    {
        return [
            'type' => $this->type()->value,
            'key' => $this->key,
            'certificate' => $this->certificate,
        ];
    }

    /**
     * {@inheritDoc}
     */
    public function type(): Type
    {
        return Type::Existing;
    }
}
