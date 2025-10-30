<?php

namespace SebastianSulinski\LaravelForgeSdk\Data;

use Carbon\Carbon;
use SebastianSulinski\LaravelForgeSdk\Enums\CertificateKeyType;
use SebastianSulinski\LaravelForgeSdk\Enums\CertificateRequestStatus;
use SebastianSulinski\LaravelForgeSdk\Enums\CertificateStatus;
use SebastianSulinski\LaravelForgeSdk\Enums\CertificateType;
use SebastianSulinski\LaravelForgeSdk\Enums\CertificateVerificationMethod;

readonly class Certificate
{
    /**
     * Certificate constructor.
     */
    public function __construct(
        public int $id,
        public int $serverId,
        public int $siteId,
        public int $domainRecordId,
        public CertificateType $type,
        public CertificateRequestStatus $requestStatus,
        public CertificateStatus $status,
        public ?CertificateVerificationMethod $verificationMethod = null,
        public ?CertificateKeyType $keyType = null,
        public ?string $preferredChain = null,
        public ?Carbon $createdAt = null,
        public ?Carbon $updatedAt = null,
    ) {}
}
