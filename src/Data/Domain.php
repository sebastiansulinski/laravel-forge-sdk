<?php

namespace SebastianSulinski\LaravelForgeSdk\Data;

use Carbon\Carbon;
use SebastianSulinski\LaravelForgeSdk\Enums\Domain\Status;
use SebastianSulinski\LaravelForgeSdk\Enums\Domain\Type;
use SebastianSulinski\LaravelForgeSdk\Enums\Site\WwwRedirectType;

readonly class Domain
{
    /**
     * Domain constructor.
     */
    public function __construct(
        public int $id,
        public int $serverId,
        public int $siteId,
        public string $name,
        public Type $type,
        public Status $status,
        public WwwRedirectType $wwwRedirectType,
        public bool $allowWildcardSubdomains,
        public ?Carbon $createdAt = null,
        public ?Carbon $updatedAt = null,
    ) {}
}
