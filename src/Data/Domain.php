<?php

namespace SebastianSulinski\LaravelForgeSdk\Data;

use Carbon\Carbon;
use SebastianSulinski\LaravelForgeSdk\Data\Concerns\HasLinks;
use SebastianSulinski\LaravelForgeSdk\Enums\Domain\Status;
use SebastianSulinski\LaravelForgeSdk\Enums\Domain\Type;
use SebastianSulinski\LaravelForgeSdk\Enums\Site\WwwRedirectType;

readonly class Domain
{
    use HasLinks;

    /**
     * Domain constructor.
     *
     * @param  array<string, mixed>  $links
     */
    public function __construct(
        public int $id,
        public string $name,
        public Type $type,
        public Status $status,
        public WwwRedirectType $wwwRedirectType,
        public bool $allowWildcardSubdomains,
        public array $links = [],
        public ?Carbon $createdAt = null,
        public ?Carbon $updatedAt = null,
    ) {}
}
