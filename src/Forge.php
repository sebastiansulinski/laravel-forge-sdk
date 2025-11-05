<?php

namespace SebastianSulinski\LaravelForgeSdk;

use Illuminate\Contracts\Container\Container;
use SebastianSulinski\LaravelForgeSdk\Actions\CreateCommand;
use SebastianSulinski\LaravelForgeSdk\Actions\CreateDatabaseSchema;
use SebastianSulinski\LaravelForgeSdk\Actions\CreateDatabaseUser;
use SebastianSulinski\LaravelForgeSdk\Actions\CreateDeployment;
use SebastianSulinski\LaravelForgeSdk\Actions\CreateDomain;
use SebastianSulinski\LaravelForgeSdk\Actions\CreateDomainCertificate;
use SebastianSulinski\LaravelForgeSdk\Actions\CreateSite;
use SebastianSulinski\LaravelForgeSdk\Actions\DeleteDatabaseSchema;
use SebastianSulinski\LaravelForgeSdk\Actions\DeleteDatabaseUser;
use SebastianSulinski\LaravelForgeSdk\Actions\DeleteSite;
use SebastianSulinski\LaravelForgeSdk\Actions\GetDeploymentScript;
use SebastianSulinski\LaravelForgeSdk\Actions\GetDeploymentStatus;
use SebastianSulinski\LaravelForgeSdk\Actions\GetDomain;
use SebastianSulinski\LaravelForgeSdk\Actions\GetDomainCertificate;
use SebastianSulinski\LaravelForgeSdk\Actions\GetEnvContent;
use SebastianSulinski\LaravelForgeSdk\Actions\GetNginxTemplateByName;
use SebastianSulinski\LaravelForgeSdk\Actions\GetServer;
use SebastianSulinski\LaravelForgeSdk\Actions\GetSite;
use SebastianSulinski\LaravelForgeSdk\Actions\ListCommands;
use SebastianSulinski\LaravelForgeSdk\Actions\ListDatabaseSchemas;
use SebastianSulinski\LaravelForgeSdk\Actions\ListDatabaseUsers;
use SebastianSulinski\LaravelForgeSdk\Actions\ListDeployments;
use SebastianSulinski\LaravelForgeSdk\Actions\ListDomains;
use SebastianSulinski\LaravelForgeSdk\Actions\ListServers;
use SebastianSulinski\LaravelForgeSdk\Actions\ListSites;
use SebastianSulinski\LaravelForgeSdk\Actions\UpdateDeploymentScript;
use SebastianSulinski\LaravelForgeSdk\Actions\UpdateEnvContent;
use SebastianSulinski\LaravelForgeSdk\Actions\UpdateSite;
use SebastianSulinski\LaravelForgeSdk\Data\Certificate;
use SebastianSulinski\LaravelForgeSdk\Data\Database;
use SebastianSulinski\LaravelForgeSdk\Data\DatabaseUser;
use SebastianSulinski\LaravelForgeSdk\Data\Deployment;
use SebastianSulinski\LaravelForgeSdk\Data\DeploymentScriptResource;
use SebastianSulinski\LaravelForgeSdk\Data\DeploymentStatus;
use SebastianSulinski\LaravelForgeSdk\Data\Domain;
use SebastianSulinski\LaravelForgeSdk\Data\ListResponse;
use SebastianSulinski\LaravelForgeSdk\Data\NginxTemplate;
use SebastianSulinski\LaravelForgeSdk\Data\Server;
use SebastianSulinski\LaravelForgeSdk\Data\Site;
use SebastianSulinski\LaravelForgeSdk\Payload\Certificate\CreatePayload as CreateCertificatePayload;
use SebastianSulinski\LaravelForgeSdk\Payload\Command\ListPayload as ListCommandsPayload;
use SebastianSulinski\LaravelForgeSdk\Payload\Database\CreateSchemaPayload;
use SebastianSulinski\LaravelForgeSdk\Payload\Database\CreateUserPayload;
use SebastianSulinski\LaravelForgeSdk\Payload\Database\ListSchemasPayload;
use SebastianSulinski\LaravelForgeSdk\Payload\Database\ListUsersPayload;
use SebastianSulinski\LaravelForgeSdk\Payload\Deployment\ListPayload as ListDeploymentsPayload;
use SebastianSulinski\LaravelForgeSdk\Payload\Deployment\UpdateScriptPayload;
use SebastianSulinski\LaravelForgeSdk\Payload\Domain\CreatePayload as CreateDomainPayload;
use SebastianSulinski\LaravelForgeSdk\Payload\Env\UpdatePayload as UpdateEnvPayload;
use SebastianSulinski\LaravelForgeSdk\Payload\Server\CreateCommandPayload;
use SebastianSulinski\LaravelForgeSdk\Payload\Server\ListPayload as ListServersPayload;
use SebastianSulinski\LaravelForgeSdk\Payload\Site\CreatePayload as CreateSitePayload;
use SebastianSulinski\LaravelForgeSdk\Payload\Site\ListPayload as ListSitesPayload;
use SebastianSulinski\LaravelForgeSdk\Payload\Site\UpdatePayload as UpdateSitePayload;

readonly class Forge
{
    /**
     * ForgeService constructor.
     */
    public function __construct(private Container $app) {}

    /**
     * Get the collection of all servers.
     *
     * @throws \Illuminate\Http\Client\ConnectionException
     * @throws \Illuminate\Http\Client\RequestException
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function listServers(
        ListServersPayload $payload = new ListServersPayload
    ): ListResponse {
        return $this->app->make(ListServers::class)
            ->handle($payload);
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
     * @throws \Illuminate\Http\Client\ConnectionException
     * @throws \Illuminate\Http\Client\RequestException
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function listSites(
        int $serverId,
        ListSitesPayload $payload = new ListSitesPayload
    ): ListResponse {
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
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function updateSite(int $serverId, int $siteId, UpdateSitePayload $payload): bool
    {
        return $this->app->make(UpdateSite::class)->handle(
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
    public function deleteSite(int $serverId, int $siteId): bool
    {
        return $this->app->make(DeleteSite::class)
            ->handle(serverId: $serverId, siteId: $siteId);
    }

    /**
     * List commands for a site.
     *
     * @throws \Illuminate\Http\Client\ConnectionException
     * @throws \Illuminate\Http\Client\RequestException
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function listCommands(
        int $serverId,
        int $siteId,
        ListCommandsPayload $payload = new ListCommandsPayload,
    ): ListResponse {
        return $this->app->make(ListCommands::class)->handle(
            serverId: $serverId,
            siteId: $siteId,
            payload: $payload
        );
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
        return $this->app->make(CreateCommand::class)->handle(
            serverId: $serverId,
            siteId: $siteId,
            payload: new CreateCommandPayload(
                command: $command
            )
        );
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
    public function updateEnvContent(int $serverId, int $siteId, UpdateEnvPayload $payload): bool
    {
        return $this->app->make(UpdateEnvContent::class)->handle(
            serverId: $serverId,
            siteId: $siteId,
            payload: $payload
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
    public function createDatabaseSchema(int $serverId, CreateSchemaPayload $payload): Database
    {
        return $this->app->make(CreateDatabaseSchema::class)->handle(
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
    public function deleteDatabaseSchema(int $serverId, int $databaseId): bool
    {
        return $this->app->make(DeleteDatabaseSchema::class)
            ->handle(serverId: $serverId, databaseId: $databaseId);
    }

    /**
     * List database schemas.
     *
     * @throws \Illuminate\Http\Client\ConnectionException
     * @throws \Illuminate\Http\Client\RequestException
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function listDatabaseSchemas(
        int $serverId,
        ListSchemasPayload $payload = new ListSchemasPayload
    ): ListResponse {
        return $this->app->make(ListDatabaseSchemas::class)->handle(
            serverId: $serverId, payload: $payload
        );
    }

    /**
     * List database users.
     *
     * @throws \Illuminate\Http\Client\ConnectionException
     * @throws \Illuminate\Http\Client\RequestException
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function listDatabaseUsers(
        int $serverId,
        ListUsersPayload $payload = new ListUsersPayload
    ): ListResponse {
        return $this->app->make(ListDatabaseUsers::class)->handle(
            serverId: $serverId, payload: $payload
        );
    }

    /**
     * Create a database user.
     *
     * @throws \Illuminate\Http\Client\ConnectionException
     * @throws \Illuminate\Http\Client\RequestException
     * @throws \Exception
     */
    public function createDatabaseUser(int $serverId, CreateUserPayload $payload): DatabaseUser
    {
        return $this->app->make(CreateDatabaseUser::class)->handle(
            serverId: $serverId,
            payload: $payload
        );
    }

    /**
     * Delete database user.
     *
     * @throws \Illuminate\Http\Client\ConnectionException
     * @throws \Illuminate\Http\Client\RequestException
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function deleteDatabaseUser(int $serverId, int $databaseUserId): bool
    {
        return $this->app->make(DeleteDatabaseUser::class)
            ->handle(serverId: $serverId, databaseUserId: $databaseUserId);
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
     * @throws \SebastianSulinski\LaravelForgeSdk\Exceptions\RequestFailed
     */
    public function updateDeploymentScript(
        int $serverId,
        int $siteId,
        UpdateScriptPayload $payload
    ): DeploymentScriptResource {
        return $this->app->make(UpdateDeploymentScript::class)->handle(
            serverId: $serverId,
            siteId: $siteId,
            payload: $payload
        );
    }

    /**
     * List deployments.
     *
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     * @throws \Illuminate\Http\Client\ConnectionException
     * @throws \Illuminate\Http\Client\RequestException
     */
    public function listDeployments(
        int $serverId,
        int $siteId,
        ListDeploymentsPayload $payload = new ListDeploymentsPayload,
    ): ListResponse {
        return $this->app->make(ListDeployments::class)->handle(
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
    ): Domain {
        return $this->app->make(CreateDomain::class)
            ->handle(
                serverId: $serverId,
                siteId: $siteId,
                payload: $payload
            );
    }

    /**
     * Get domain.
     *
     * @throws \Illuminate\Http\Client\ConnectionException
     * @throws \Illuminate\Http\Client\RequestException
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     * @throws \SebastianSulinski\LaravelForgeSdk\Exceptions\RequestFailed
     */
    public function getDomain(int $serverId, int $siteId, int $domainRecordId): Domain
    {
        return $this->app->make(GetDomain::class)->handle(
            serverId: $serverId,
            siteId: $siteId,
            domainRecordId: $domainRecordId
        );
    }

    /**
     * List domains.
     *
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     * @throws \Illuminate\Http\Client\ConnectionException
     * @throws \Illuminate\Http\Client\RequestException
     */
    public function listDomains(int $serverId, int $siteId): ListResponse
    {
        return $this->app->make(ListDomains::class)->handle(
            serverId: $serverId,
            siteId: $siteId
        );
    }

    /**
     * Get nginx template by name.
     *
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     * @throws \Illuminate\Http\Client\ConnectionException
     * @throws \Illuminate\Http\Client\RequestException
     * @throws \SebastianSulinski\LaravelForgeSdk\Exceptions\RequestFailed
     */
    public function getNginxTemplateByName(int $serverId, string $templateName): ?NginxTemplate
    {
        return $this->app->make(GetNginxTemplateByName::class)
            ->handle(serverId: $serverId, templateName: $templateName);
    }
}
