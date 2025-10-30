<?php

namespace SebastianSulinski\LaravelForgeSdk\Payload;

use Illuminate\Contracts\Support\Arrayable;
use SebastianSulinski\LaravelForgeSdk\Enums\CertificateType;

/**
 * @implements Arrayable<string, mixed>
 */
abstract readonly class CreateCertificatePayload implements Arrayable
{
    /**
     * Get certificate type.
     */
    abstract public function type(): CertificateType;
}
