<?php

namespace SebastianSulinski\LaravelForgeSdk\Traits;

use SebastianSulinski\LaravelForgeSdk\Data\DeploymentScriptResource;

/**
 * @phpstan-type DeploymentScriptResourceData array{
 *     id: string,
 *     attributes: array{
 *         content: string|null,
 *         auto_source: bool
 *     },
 *     links?: array<string, mixed>
 * }
 */
trait HasDeploymentScriptResource
{
    /**
     * Make a deployment script resource.
     *
     * @param  DeploymentScriptResourceData  $data
     */
    protected function makeDeploymentScriptResource(array $data): DeploymentScriptResource
    {
        $attributes = $data['attributes'];

        return new DeploymentScriptResource(
            id: $data['id'],
            content: $attributes['content'],
            autoSource: $attributes['auto_source'],
            links: $data['links'] ?? [],
        );
    }
}
