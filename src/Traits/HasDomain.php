<?php

namespace SebastianSulinski\LaravelForgeSdk\Traits;

use SebastianSulinski\LaravelForgeSdk\Data\Domain;
use SebastianSulinski\LaravelForgeSdk\Enums\DomainStatus;
use SebastianSulinski\LaravelForgeSdk\Enums\DomainType;
use SebastianSulinski\LaravelForgeSdk\Enums\WwwRedirectType;
use Carbon\Carbon;

trait HasDomain
{
    /**
     * Make domain.
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
