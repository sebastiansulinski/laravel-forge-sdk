<?php

namespace SebastianSulinski\LaravelForgeSdk\Traits;

use Carbon\Carbon;
use SebastianSulinski\LaravelForgeSdk\Data\MaintenanceMode;
use SebastianSulinski\LaravelForgeSdk\Data\Repository;
use SebastianSulinski\LaravelForgeSdk\Data\Site;
use SebastianSulinski\LaravelForgeSdk\Enums\MaintenanceModeStatus;
use SebastianSulinski\LaravelForgeSdk\Enums\RepositoryStatus;
use SebastianSulinski\LaravelForgeSdk\Enums\SiteStatus;

trait HasSite
{
    /**
     * Make site.
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
