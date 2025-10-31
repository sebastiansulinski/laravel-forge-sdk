<?php

namespace SebastianSulinski\LaravelForgeSdk\Traits;

use Carbon\Carbon;
use SebastianSulinski\LaravelForgeSdk\Data\Domain;
use SebastianSulinski\LaravelForgeSdk\Enums\DomainStatus;
use SebastianSulinski\LaravelForgeSdk\Enums\DomainType;
use SebastianSulinski\LaravelForgeSdk\Enums\WwwRedirectType;

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
 *     }
 * }
 */
trait HasDomain
{
    /**
     * Make domain.
     *
     * @param  DomainData  $data
     */
    protected function makeDomain(int $serverId, int $siteId, array $data): Domain
    {
        $attributes = $data['attributes'];

        return new Domain(
            id: $data['id'],
            serverId: $serverId,
            siteId: $siteId,
            name: $attributes['name'],
            type: DomainType::from($attributes['type']),
            status: DomainStatus::from($attributes['status']),
            wwwRedirectType: WwwRedirectType::from($attributes['www_redirect_type']),
            allowWildcardSubdomains: $attributes['allow_wildcard_subdomains'],
            createdAt: isset($attributes['created_at'])
                ? Carbon::parse($attributes['created_at'])
                : null,
            updatedAt: isset($attributes['updated_at'])
                ? Carbon::parse($attributes['updated_at'])
                : null,
        );
    }
}
