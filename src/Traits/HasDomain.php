<?php

namespace SebastianSulinski\LaravelForgeSdk\Traits;

use Carbon\Carbon;
use SebastianSulinski\LaravelForgeSdk\Data\Domain;
use SebastianSulinski\LaravelForgeSdk\Enums\Domain\Status;
use SebastianSulinski\LaravelForgeSdk\Enums\Domain\Type;
use SebastianSulinski\LaravelForgeSdk\Enums\Site\WwwRedirectType;

/**
 * @phpstan-type DomainData array{
 *     id: int,
 *     attributes: array{
 *         name: string,
 *         type: string,
 *         status: string,
 *         www_redirect_type: string,
 *         allow_wildcard_subdomains: bool,
 *         created_at?: string|null,
 *         updated_at?: string|null
 *     },
 *     links?: array<string, mixed>
 * }
 */
trait HasDomain
{
    /**
     * Make domain.
     *
     * @param  DomainData  $data
     */
    protected function makeDomain(array $data): Domain
    {
        $attributes = $data['attributes'];

        return new Domain(
            id: $data['id'],
            name: $attributes['name'],
            type: Type::from($attributes['type']),
            status: Status::from($attributes['status']),
            wwwRedirectType: WwwRedirectType::from($attributes['www_redirect_type']),
            allowWildcardSubdomains: $attributes['allow_wildcard_subdomains'],
            links: $data['links'] ?? [],
            createdAt: isset($attributes['created_at'])
                ? Carbon::parse($attributes['created_at'])
                : null,
            updatedAt: isset($attributes['updated_at'])
                ? Carbon::parse($attributes['updated_at'])
                : null,
        );
    }
}
