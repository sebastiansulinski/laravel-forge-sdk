<?php

namespace SebastianSulinski\LaravelForgeSdk\Payload;

use SebastianSulinski\LaravelForgeSdk\Enums\CertificateKeyType;
use SebastianSulinski\LaravelForgeSdk\Enums\CertificateType;
use SebastianSulinski\LaravelForgeSdk\Enums\CertificateVerificationMethod;

readonly class CreateLetsEncryptCertificatePayload extends CreateCertificatePayload
{
    /**
     * CreateLetsEncryptCertificatePayload constructor.
     */
    public function __construct(
        public CertificateVerificationMethod $verification_method,
        public CertificateKeyType $key_type,
        public string $preferred_chain = 'ISRG Root X1',
    ) {}

    /**
     * Transform data for API request.
     */
    public function toArray(): array
    {
        return [
            'type' => $this->type()->value,
            'letsencrypt' => [
                'verification_method' => $this->verification_method->value,
                'key_type' => $this->key_type->value,
                'preferred_chain' => $this->preferred_chain,
            ],
        ];
    }

    /**
     * {@inheritDoc}
     */
    public function type(): CertificateType
    {
        return CertificateType::LetsEncrypt;
    }
}
