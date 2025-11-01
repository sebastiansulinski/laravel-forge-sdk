<?php

namespace SebastianSulinski\LaravelForgeSdk\Payload\Certificate;

use SebastianSulinski\LaravelForgeSdk\Enums\Certificate\KeyType;
use SebastianSulinski\LaravelForgeSdk\Enums\Certificate\Type;
use SebastianSulinski\LaravelForgeSdk\Enums\Certificate\VerificationMethod;

readonly class CreateLetsEncryptPayload extends CreatePayload
{
    /**
     * CreateLetsEncryptCertificatePayload constructor.
     */
    public function __construct(
        public VerificationMethod $verification_method,
        public KeyType $key_type,
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
    public function type(): Type
    {
        return Type::LetsEncrypt;
    }
}
