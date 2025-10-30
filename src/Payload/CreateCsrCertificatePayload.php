<?php

namespace SebastianSulinski\LaravelForgeSdk\Payload;

use SebastianSulinski\LaravelForgeSdk\Enums\CertificateType;

readonly class CreateCsrCertificatePayload extends CreateCertificatePayload
{
    /**
     * CreateCsrCertificatePayload constructor.
     */
    public function __construct(
        public string $domain,
        public string $country,
        public string $state,
        public string $city,
        public string $organization,
        public string $department,
        public ?array $sans = null,
    ) {}

    /**
     * {@inheritDoc}
     */
    public function toArray(): array
    {
        return array_filter([
            'type' => $this->type()->value,
            'domain' => $this->domain,
            'country' => $this->country,
            'state' => $this->state,
            'city' => $this->city,
            'organization' => $this->organization,
            'department' => $this->department,
            'sans' => $this->sans,
        ], fn ($value) => $value !== null);
    }

    /**
     * {@inheritDoc}
     */
    public function type(): CertificateType
    {
        return CertificateType::Csr;
    }
}
