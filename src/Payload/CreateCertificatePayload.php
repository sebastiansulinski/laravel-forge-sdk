<?php

namespace SebastianSulinski\LaravelForgeSdk\Payload;

use SebastianSulinski\LaravelForgeSdk\Enums\CertificateType;
use Illuminate\Contracts\Support\Arrayable;

abstract readonly class CreateCertificatePayload implements Arrayable
{
    /**
     * Get certificate type.
     */
    abstract public function type(): CertificateType;
}
