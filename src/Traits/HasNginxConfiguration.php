<?php

namespace SebastianSulinski\LaravelForgeSdk\Traits;

use SebastianSulinski\LaravelForgeSdk\Data\NginxConfiguration;

/**
 * @phpstan-type NginxConfigurationData array{
 *     id: string,
 *     attributes: array{
 *         content: string
 *     }
 * }
 */
trait HasNginxConfiguration
{
    /**
     * Make nginx configuration.
     *
     * @param  NginxConfigurationData  $data
     */
    protected function makeNginxConfiguration(array $data): NginxConfiguration
    {
        $attributes = $data['attributes'];

        return new NginxConfiguration(
            id: $data['id'],
            content: $attributes['content'],
        );
    }
}
