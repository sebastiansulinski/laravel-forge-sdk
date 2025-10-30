<?php

namespace SebastianSulinski\LaravelForgeSdk\Data;

use Carbon\Carbon;
use SebastianSulinski\LaravelForgeSdk\Enums\SiteStatus;

readonly class Site
{
    /**
     * Site constructor.
     *
     * @param  array<int, string>  $aliases
     * @param  array<int, string>  $sharedPaths
     */
    public function __construct(
        public int $id,
        public string $name,
        public SiteStatus $status,
        public string $url,
        public string $user,
        public bool $https,
        public string $webDirectory,
        public string $rootDirectory,
        public array $aliases,
        public string $phpVersion,
        public ?string $deploymentStatus,
        public bool $isolated,
        public array $sharedPaths,
        public bool $zeroDowntimeDeployments,
        public string $appType,
        public bool $usesEnvoyer,
        public string $deploymentUrl,
        public Repository $repository,
        public MaintenanceMode $maintenanceMode,
        public ?bool $quickDeploy = null,
        public ?string $deploymentScript = null,
        public ?bool $wildcards = null,
        public ?string $healthcheckUrl = null,
        public ?Carbon $createdAt = null,
        public ?Carbon $updatedAt = null,
    ) {}
}
