<?php

namespace SebastianSulinski\LaravelForgeSdk;

use Illuminate\Contracts\Container\Container;
use Illuminate\Support\Collection;
use SebastianSulinski\LaravelForgeSdk\Actions\CreateCommand;
use SebastianSulinski\LaravelForgeSdk\Actions\CreateDatabase;
use SebastianSulinski\LaravelForgeSdk\Actions\CreateDeployment;
use SebastianSulinski\LaravelForgeSdk\Actions\CreateDomain;
use SebastianSulinski\LaravelForgeSdk\Actions\CreateDomainCertificate;
use SebastianSulinski\LaravelForgeSdk\Actions\CreateSite;
use SebastianSulinski\LaravelForgeSdk\Actions\DeleteDatabaseSchema;
use SebastianSulinski\LaravelForgeSdk\Actions\DeleteDatabaseUser;
use SebastianSulinski\LaravelForgeSdk\Actions\DeleteSite;
use SebastianSulinski\LaravelForgeSdk\Actions\GetDeploymentScript;
use SebastianSulinski\LaravelForgeSdk\Actions\GetDeploymentStatus;
use SebastianSulinski\LaravelForgeSdk\Actions\GetDomainCertificate;
use SebastianSulinski\LaravelForgeSdk\Actions\GetEnvContent;
use SebastianSulinski\LaravelForgeSdk\Actions\GetServer;
use SebastianSulinski\LaravelForgeSdk\Actions\GetSite;
use SebastianSulinski\LaravelForgeSdk\Actions\ListServers;
use SebastianSulinski\LaravelForgeSdk\Actions\ListSites;
use SebastianSulinski\LaravelForgeSdk\Actions\UpdateDeploymentScript;
use SebastianSulinski\LaravelForgeSdk\Actions\UpdateEnvContent;
use SebastianSulinski\LaravelForgeSdk\Actions\UpdateSite;
use SebastianSulinski\LaravelForgeSdk\Data\Certificate;
use SebastianSulinski\LaravelForgeSdk\Data\Database;
use SebastianSulinski\LaravelForgeSdk\Data\Deployment;
use SebastianSulinski\LaravelForgeSdk\Data\DeploymentStatus;
use SebastianSulinski\LaravelForgeSdk\Data\Server;
use SebastianSulinski\LaravelForgeSdk\Data\Site;
use SebastianSulinski\LaravelForgeSdk\Payload\CreateCertificatePayload;
use SebastianSulinski\LaravelForgeSdk\Payload\CreateCommandPayload;
use SebastianSulinski\LaravelForgeSdk\Payload\CreateDatabasePayload;
use SebastianSulinski\LaravelForgeSdk\Payload\CreateDomainPayload;
use SebastianSulinski\LaravelForgeSdk\Payload\CreateSitePayload;
use SebastianSulinski\LaravelForgeSdk\Payload\ListServersPayload;
use SebastianSulinski\LaravelForgeSdk\Payload\ListSitesPayload;
use SebastianSulinski\LaravelForgeSdk\Payload\UpdateDeploymentScriptPayload;
use SebastianSulinski\LaravelForgeSdk\Payload\UpdateEnvContentPayload;
use SebastianSulinski\LaravelForgeSdk\Payload\UpdateSitePayload;

readonly class Forge
{
    /**
     * ForgeService constructor.
     */
    public function __construct(private Container $app) {}

    /**
     * Get the collection of all servers.
     *
     * @return Collection<int, Server>
     *
     * @throws \Illuminate\Http\Client\ConnectionException
     * @throws \Illuminate\Http\Client\RequestException
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function listServers(
        ListServersPayload $payload = new ListServersPayload
    ): Collection {
        return $this->app->make(ListServers::class)
            ->handle(payload: $payload);
    }

    /**
     * Get server.
     *
     * @throws \Illuminate\Http\Client\ConnectionException
     * @throws \Illuminate\Http\Client\RequestException
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     * @throws \SebastianSulinski\LaravelForgeSdk\Exceptions\RequestFailed
     */
    public function getServer(int $serverId): Server
    {
        return $this->app->make(GetServer::class)->handle($serverId);
    }

    /**
     * Get the collection of all server sites.
     *
     * @return Collection<int, Site>
     *
     * @throws \Illuminate\Http\Client\ConnectionException
     * @throws \Illuminate\Http\Client\RequestException
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function listSites(
        int $serverId,
        ListSitesPayload $payload = new ListSitesPayload
    ): Collection {
        return $this->app->make(ListSites::class)->handle(
            serverId: $serverId, payload: $payload
        );
    }

    /**
     * Get a site instance.
     *
     * @throws \Illuminate\Http\Client\ConnectionException
     * @throws \Illuminate\Http\Client\RequestException
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     * @throws \SebastianSulinski\LaravelForgeSdk\Exceptions\RequestFailed
     */
    public function getSite(int $siteId): Site
    {
        return $this->app->make(GetSite::class)
            ->handle(siteId: $siteId);
    }

    /**
     * Create a new site.
     *
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     * @throws \Illuminate\Http\Client\ConnectionException
     * @throws \Illuminate\Http\Client\RequestException
     * @throws \SebastianSulinski\LaravelForgeSdk\Exceptions\RequestFailed
     */
    public function createSite(int $serverId, CreateSitePayload $payload): Site
    {
        return $this->app->make(CreateSite::class)->handle(
            serverId: $serverId,
            payload: $payload,
        );
    }

    /**
     * Update site.
     *
     * @throws \Illuminate\Http\Client\ConnectionException
     * @throws \Illuminate\Http\Client\RequestException
     * @throws \Exception
     */
    public function updateSite(int $serverId, int $siteId, UpdateSitePayload $payload): void
    {
        $this->app->make(UpdateSite::class)->handle(
            serverId: $serverId,
            siteId: $siteId,
            payload: $payload
        );
    }

    /**
     * Delete a site.
     *
     * @throws \Illuminate\Http\Client\ConnectionException
     * @throws \Illuminate\Http\Client\RequestException
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function deleteSite(int $serverId, int $siteId): void
    {
        $this->app->make(DeleteSite::class)
            ->handle(serverId: $serverId, siteId: $siteId);
    }

    /**
     * Create site command
     *
     * @throws \Illuminate\Http\Client\ConnectionException
     * @throws \Illuminate\Http\Client\RequestException
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function createCommand(int $serverId, int $siteId, string $command): bool
    {
        $this->app->make(CreateCommand::class)->handle(
            serverId: $serverId,
            siteId: $siteId,
            payload: new CreateCommandPayload(
                command: $command
            )
        );

        return true;
    }

    /**
     * Get the content of the site's Environment file.
     *
     * @throws \Illuminate\Http\Client\ConnectionException
     * @throws \Illuminate\Http\Client\RequestException
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function getEnvContent(int $serverId, int $siteId): string
    {
        return $this->app->make(GetEnvContent::class)
            ->handle(serverId: $serverId, siteId: $siteId);
    }

    /**
     * Update the content of the site's Environment file.
     *
     * @throws \Illuminate\Http\Client\ConnectionException
     * @throws \Illuminate\Http\Client\RequestException
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function updateEnvContent(int $serverId, int $siteId, string $content): void
    {
        $this->app->make(UpdateEnvContent::class)->handle(
            serverId: $serverId,
            siteId: $siteId,
            payload: new UpdateEnvContentPayload(environment: $content)
        );
    }

    /**
     * Deploy the given site.
     *
     * @throws \Illuminate\Http\Client\ConnectionException
     * @throws \Illuminate\Http\Client\RequestException
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     * @throws \SebastianSulinski\LaravelForgeSdk\Exceptions\RequestFailed
     */
    public function createDeployment(int $serverId, int $siteId): Deployment
    {
        return $this->app->make(CreateDeployment::class)->handle(
            serverId: $serverId, siteId: $siteId
        );
    }

    /**
     * Get the last deployment timestamp.
     *
     * @throws \Illuminate\Http\Client\ConnectionException
     * @throws \Illuminate\Http\Client\RequestException
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     * @throws \SebastianSulinski\LaravelForgeSdk\Exceptions\RequestFailed
     */
    public function getDeploymentStatus(int $serverId, int $siteId): DeploymentStatus
    {
        return $this->app->make(GetDeploymentStatus::class)
            ->handle(serverId: $serverId, siteId: $siteId);
    }

    /**
     * Create a database.
     *
     * @throws \Illuminate\Http\Client\ConnectionException
     * @throws \Illuminate\Http\Client\RequestException
     * @throws \Exception
     */
    public function createDatabase(int $serverId, CreateDatabasePayload $payload): Database
    {
        return $this->app->make(CreateDatabase::class)->handle(
            serverId: $serverId,
            payload: $payload
        );
    }

    /**
     * Delete database.
     *
     * @throws \Illuminate\Http\Client\ConnectionException
     * @throws \Illuminate\Http\Client\RequestException
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function deleteDatabaseSchema(int $serverId, int $databaseId): void
    {
        $this->app->make(DeleteDatabaseSchema::class)
            ->handle(serverId: $serverId, databaseId: $databaseId);
    }

    /**
     * Delete database user.
     *
     * @throws \Illuminate\Http\Client\ConnectionException
     * @throws \Illuminate\Http\Client\RequestException
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function deleteDatabaseUser(int $serverId, int $databaseUserId): void
    {
        $this->app->make(DeleteDatabaseUser::class)
            ->handle($serverId, $databaseUserId);
    }

    /**
     * Install new certificate.
     *
     * @throws \Illuminate\Http\Client\ConnectionException
     * @throws \Illuminate\Http\Client\RequestException
     * @throws \Exception
     */
    public function createDomainCertificate(
        int $serverId,
        int $siteId,
        int $domainRecordId,
        CreateCertificatePayload $payload
    ): Certificate {

        return $this->app->make(CreateDomainCertificate::class)->handle(
            serverId: $serverId,
            siteId: $siteId,
            domainRecordId: $domainRecordId,
            payload: $payload
        );
    }

    /**
     * Get domain certificate.
     *
     * @throws \Illuminate\Http\Client\ConnectionException
     * @throws \Illuminate\Http\Client\RequestException
     * @throws \Exception
     */
    public function getDomainCertificate(
        int $serverId,
        int $siteId,
        int $domainRecordId
    ): Certificate {

        return $this->app->make(GetDomainCertificate::class)->handle(
            serverId: $serverId,
            siteId: $siteId,
            domainRecordId: $domainRecordId,
        );
    }

    /**
     * Get deployment script.
     *
     * @throws \Illuminate\Http\Client\ConnectionException
     * @throws \Illuminate\Http\Client\RequestException
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function getDeploymentScript(int $serverId, int $siteId): string
    {
        return $this->app->make(GetDeploymentScript::class)
            ->handle(serverId: $serverId, siteId: $siteId);
    }

    /**
     * Update deployment script.
     *
     * @throws \Illuminate\Http\Client\ConnectionException
     * @throws \Illuminate\Http\Client\RequestException
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function updateDeploymentScript(
        int $serverId,
        int $siteId,
        UpdateDeploymentScriptPayload $payload
    ): void {
        $this->app->make(UpdateDeploymentScript::class)->handle(
            serverId: $serverId,
            siteId: $siteId,
            payload: $payload
        );
    }

    /**
     * Create a domain.
     *
     * @throws \Illuminate\Http\Client\ConnectionException
     * @throws \Illuminate\Http\Client\RequestException
     * @throws \Exception
     */
    public function createDomain(
        int $serverId,
        int $siteId,
        CreateDomainPayload $payload
    ): void {
        $this->app->make(CreateDomain::class)
            ->handle(
                serverId: $serverId,
                siteId: $siteId,
                payload: $payload
            );
    }
}
