<?php

namespace SebastianSulinski\LaravelForgeSdk\Traits;

use Carbon\Carbon;
use SebastianSulinski\LaravelForgeSdk\Data\NginxTemplate;

/**
 * @phpstan-type NginxTemplateData array{
 *     id: int,
 *     attributes: array{
 *         name: string,
 *         content: string,
 *         created_at?: string|null,
 *         updated_at?: string|null
 *     }
 * }
 */
trait HasNginxTemplate
{
    /**
     * Make nginx template.
     *
     * @param  NginxTemplateData  $data
     */
    protected function makeNginxTemplate(array $data): NginxTemplate
    {
        $attributes = $data['attributes'];

        return new NginxTemplate(
            id: $data['id'],
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
