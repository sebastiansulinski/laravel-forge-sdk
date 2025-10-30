<?php

namespace SebastianSulinski\LaravelForgeSdk\Data;

use SebastianSulinski\LaravelForgeSdk\Enums\DomainStatus;
use SebastianSulinski\LaravelForgeSdk\Enums\DomainType;
use SebastianSulinski\LaravelForgeSdk\Enums\WwwRedirectType;
use Carbon\Carbon;

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
        public DomainType $type,
        public DomainStatus $status,
        public WwwRedirectType $wwwRedirectType,
        public bool $allowWildcardSubdomains,
        public ?Carbon $createdAt = null,
        public ?Carbon $updatedAt = null,
    ) {}
}
