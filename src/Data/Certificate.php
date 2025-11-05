<?php

namespace SebastianSulinski\LaravelForgeSdk\Data;

use Carbon\Carbon;
use SebastianSulinski\LaravelForgeSdk\Enums\Certificate\KeyType;
use SebastianSulinski\LaravelForgeSdk\Enums\Certificate\RequestStatus;
use SebastianSulinski\LaravelForgeSdk\Enums\Certificate\Status;
use SebastianSulinski\LaravelForgeSdk\Enums\Certificate\Type;
use SebastianSulinski\LaravelForgeSdk\Enums\Certificate\VerificationMethod;

readonly class Certificate
{
    /**
     * Certificate constructor.
     */
    public function __construct(
        public int $id,
        public Type $type,
        public RequestStatus $requestStatus,
        public Status $status,
        public ?VerificationMethod $verificationMethod = null,
        public ?KeyType $keyType = null,
        public ?string $preferredChain = null,
        public ?Carbon $createdAt = null,
        public ?Carbon $updatedAt = null,
    ) {}
}
