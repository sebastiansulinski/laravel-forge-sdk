<?php

namespace SebastianSulinski\LaravelForgeSdk\Facades;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Facade;
use SebastianSulinski\LaravelForgeSdk\Data\Certificate;
use SebastianSulinski\LaravelForgeSdk\Data\Database;
use SebastianSulinski\LaravelForgeSdk\Data\DatabaseUser;
use SebastianSulinski\LaravelForgeSdk\Data\Deployment;
use SebastianSulinski\LaravelForgeSdk\Data\DeploymentStatus;
use SebastianSulinski\LaravelForgeSdk\Data\Domain;
use SebastianSulinski\LaravelForgeSdk\Data\NginxTemplate;
use SebastianSulinski\LaravelForgeSdk\Data\Server;
use SebastianSulinski\LaravelForgeSdk\Data\Site;
use SebastianSulinski\LaravelForgeSdk\Forge as ForgeService;
use SebastianSulinski\LaravelForgeSdk\Payload\CreateCertificatePayload;
use SebastianSulinski\LaravelForgeSdk\Payload\CreateDatabasePayload;
use SebastianSulinski\LaravelForgeSdk\Payload\CreateDomainPayload;
use SebastianSulinski\LaravelForgeSdk\Payload\CreateSitePayload;
use SebastianSulinski\LaravelForgeSdk\Payload\ListDatabaseSchemasPayload;
use SebastianSulinski\LaravelForgeSdk\Payload\ListDatabaseUsersPayload;
use SebastianSulinski\LaravelForgeSdk\Payload\ListDeploymentsPayload;
use SebastianSulinski\LaravelForgeSdk\Payload\ListServersPayload;
use SebastianSulinski\LaravelForgeSdk\Payload\ListSitesPayload;
use SebastianSulinski\LaravelForgeSdk\Payload\UpdateDeploymentScriptPayload;
use SebastianSulinski\LaravelForgeSdk\Payload\UpdateEnvContentPayload;
use SebastianSulinski\LaravelForgeSdk\Payload\UpdateSitePayload;

/**
 * @method static Collection<int, Server> listServers(ListServersPayload $payload)
 * @method static Server getServer(int $serverId)
 * @method static Collection<int, Site> listSites(int $serverId, ListSitesPayload $payload)
 * @method static Site getSite(int $siteId)
 * @method static Site createSite(int $serverId, CreateSitePayload $payload)
 * @method static void updateSite(int $serverId, int $siteId, UpdateSitePayload $payload)
 * @method static void deleteSite(int $serverId, int $siteId)
 * @method static bool createCommand(int $serverId, int $siteId, string $command)
 * @method static string getEnvContent(int $serverId, int $siteId)
 * @method static void updateEnvContent(int $serverId, int $siteId, UpdateEnvContentPayload $payload)
 * @method static Deployment createDeployment(int $serverId, int $siteId)
 * @method static DeploymentStatus getDeploymentStatus(int $serverId, int $siteId)
 * @method static Database createDatabase(int $serverId, CreateDatabasePayload $payload)
 * @method static void deleteDatabaseSchema(int $serverId, int $databaseId)
 * @method static Collection<int, Database> listDatabaseSchemas(int $serverId, ListDatabaseSchemasPayload $payload)
 * @method static Collection<int, DatabaseUser> listDatabaseUsers(int $serverId, ListDatabaseUsersPayload $payload)
 * @method static void deleteDatabaseUser(int $serverId, int $databaseUserId)
 * @method static Certificate createDomainCertificate(int $serverId, int $siteId, int $domainRecordId, CreateCertificatePayload $payload)
 * @method static Certificate getDomainCertificate(int $serverId, int $siteId, int $domainRecordId)
 * @method static string getDeploymentScript(int $serverId, int $siteId)
 * @method static void updateDeploymentScript(int $serverId, int $siteId, UpdateDeploymentScriptPayload $payload)
 * @method static Collection<int, Deployment> listDeployments(int $serverId, int $siteId, ListDeploymentsPayload $payload)
 * @method static void createDomain(int $serverId, int $siteId, CreateDomainPayload $payload)
 * @method static Collection<int, Domain> listDomains(int $serverId, int $siteId)
 * @method static ?NginxTemplate getNginxTemplateByName(int $serverId, string $templateName)
 *
 * @see \SebastianSulinski\LaravelForgeSdk\Forge
 */
class Forge extends Facade
{
    /**
     * Get the registered name of the component.
     */
    protected static function getFacadeAccessor(): string
    {
        return ForgeService::class;
    }
}
