<?php

namespace SebastianSulinski\LaravelForgeSdk\Traits;

use Carbon\Carbon;
use SebastianSulinski\LaravelForgeSdk\Data\MaintenanceMode;
use SebastianSulinski\LaravelForgeSdk\Data\Repository;
use SebastianSulinski\LaravelForgeSdk\Data\Site;
use SebastianSulinski\LaravelForgeSdk\Enums\Repository\Status as RepositoryStatus;
use SebastianSulinski\LaravelForgeSdk\Enums\Site\MaintenanceModeStatus;
use SebastianSulinski\LaravelForgeSdk\Enums\Site\Status as SiteStatus;

/**
 * @phpstan-type SiteData array{
 *     id: int,
 *     attributes: array{
 *         name: string,
 *         status: string,
 *         url: string,
 *         user: string,
 *         https: bool,
 *         web_directory: string,
 *         root_directory: string,
 *         aliases?: array<int, string>,
 *         php_version: string,
 *         deployment_status: string,
 *         isolated: bool,
 *         shared_paths?: array<int, string>,
 *         zero_downtime_deployments: bool,
 *         app_type: string,
 *         uses_envoyer: bool,
 *         deployment_url: string,
 *         repository: array{
 *             provider: string,
 *             url?: string|null,
 *             branch?: string|null,
 *             status?: string|null
 *         },
 *         maintenance_mode: array{
 *             enabled?: bool|null,
 *             status?: string|null
 *         },
 *         quick_deploy: bool,
 *         deployment_script: string,
 *         wildcards: bool,
 *         healthcheck_url: string,
 *         created_at?: string|null,
 *         updated_at?: string|null
 *     },
 *     relationships?: array<string, mixed>,
 *     links?: array<string, mixed>
 * }
 */
trait HasSite
{
    /**
     * Make site.
     *
     * @param  SiteData  $data
     */
    protected function makeSite(array $data): Site
    {
        $attributes = $data['attributes'];
        $repositoryData = $attributes['repository'];
        $maintenanceModeData = $attributes['maintenance_mode'];

        return new Site(
            id: $data['id'],
            name: $attributes['name'],
            status: SiteStatus::from($attributes['status']),
            url: $attributes['url'],
            user: $attributes['user'],
            https: $attributes['https'],
            webDirectory: $attributes['web_directory'],
            rootDirectory: $attributes['root_directory'],
            aliases: $attributes['aliases'] ?? [],
            phpVersion: $attributes['php_version'],
            deploymentStatus: $attributes['deployment_status'],
            isolated: $attributes['isolated'],
            sharedPaths: $attributes['shared_paths'] ?? [],
            zeroDowntimeDeployments: $attributes['zero_downtime_deployments'],
            appType: $attributes['app_type'],
            usesEnvoyer: $attributes['uses_envoyer'],
            deploymentUrl: $attributes['deployment_url'],
            repository: new Repository(
                provider: $repositoryData['provider'],
                url: $repositoryData['url'] ?? null,
                branch: $repositoryData['branch'] ?? null,
                status: isset($repositoryData['status'])
                    ? RepositoryStatus::from($repositoryData['status'])
                    : null
            ),
            maintenanceMode: new MaintenanceMode(
                enabled: $maintenanceModeData['enabled'] ?? null,
                status: isset($maintenanceModeData['status'])
                    ? MaintenanceModeStatus::from($maintenanceModeData['status'])
                    : null
            ),
            relationships: $data['relationships'] ?? [],
            links: $data['links'] ?? [],
            quickDeploy: $attributes['quick_deploy'],
            deploymentScript: $attributes['deployment_script'],
            wildcards: $attributes['wildcards'],
            healthcheckUrl: $attributes['healthcheck_url'],
            createdAt: isset($attributes['created_at'])
                ? Carbon::parse($attributes['created_at'])
                : null,
            updatedAt: isset($attributes['updated_at'])
                ? Carbon::parse($attributes['updated_at'])
                : null
        );
    }
}
