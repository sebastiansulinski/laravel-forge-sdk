<?php

namespace SebastianSulinski\LaravelForgeSdk\Traits;

use Carbon\Carbon;
use SebastianSulinski\LaravelForgeSdk\Data\NginxTemplate;

trait HasNginxTemplate
{
    /**
     * Make nginx template.
     */
    protected function makeNginxTemplate(int $serverId, array $data): NginxTemplate
    {
        $attributes = $data['attributes'];

        return new NginxTemplate(
            id: $data['id'],
            serverId: $serverId,
            name: $attributes['name'],
            content: $attributes['content'],
            createdAt: isset($attributes['created_at'])
                ? Carbon::parse($attributes['created_at'])
                : null,
            updatedAt: isset($attributes['updated_at'])
                ? Carbon::parse($attributes['updated_at'])
                : null,
        );
    }
}
