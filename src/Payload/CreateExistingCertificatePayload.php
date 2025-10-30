<?php

namespace SebastianSulinski\LaravelForgeSdk\Payload;

use SebastianSulinski\LaravelForgeSdk\Enums\CertificateType;

readonly class CreateExistingCertificatePayload extends CreateCertificatePayload
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
    public function type(): CertificateType
    {
        return CertificateType::Existing;
    }
}
