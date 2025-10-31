# Laravel Forge API V2 SDK

An unofficial, type-safe Laravel package for interacting with the Laravel Forge API V2.

> **Note:** This is an **unofficial SDK**. Laravel Forge's official SDK currently only supports API v1. This package is a work in progress and additional actions will be added over time as the Forge API v2 documentation gets updated.

## Requirements

- PHP 8.4 or 8.5
- Laravel 12

## Installation

```bash
composer require sebastiansulinski/laravel-forge-sdk
```

## Configuration

Add your Forge API credentials to your `.env` file:

```env
FORGE_TOKEN=your-forge-api-token
FORGE_TIMEOUT=90
FORGE_ORGANISATION=your-organisation-id
```

## Usage

The SDK is automatically registered via Laravel's package auto-discovery.

### Using the Forge service class

The recommended way to interact with the Forge API is by injecting the `Forge` class:

```php
use SebastianSulinski\LaravelForgeSdk\Forge;
use SebastianSulinski\LaravelForgeSdk\Payload\CreateSitePayload;
use SebastianSulinski\LaravelForgeSdk\Payload\ListServersPayload;
use SebastianSulinski\LaravelForgeSdk\Payload\ListSitesPayload;
use SebastianSulinski\LaravelForgeSdk\Enums\SiteInclude;

class DeploySiteJob
{
    public function __construct(private Forge $forge) {}

    public function handle(): void
    {
        // List all servers with filters
        $servers = $this->forge->listServers(
            new ListServersPayload(
                filterProvider: 'digitalocean',
                filterRegion: 'lon1',
                pageSize: 20
            )
        );

        // Get a specific server
        $server = $this->forge->getServer(serverId: 123);
        // Access relationships and links
        $siteRelationships = $server->relationships['sites'] ?? null;
        $selfLink = $server->links['self'] ?? null;

        // List sites with includes
        $sites = $this->forge->listSites(
            payload: new ListSitesPayload(
                filterName: 'example.com',
                include: [SiteInclude::LatestDeployment, SiteInclude::Server]
            )
        );

        // Get a specific site
        $site = $this->forge->getSite(siteId: 456);

        // Create a new site
        $site = $this->forge->createSite(
            serverId: 123,
            payload: new CreateSitePayload(
                domain: 'example.com',
                projectType: 'php',
                directory: '/public'
            )
        );

        // Trigger deployment
        $deployment = $this->forge->createDeployment(
            serverId: 123,
            siteId: $site->id
        );
    }
}
```

### Using Individual Action Classes

For more granular control, you can inject specific Action classes directly:

```php
use SebastianSulinski\LaravelForgeSdk\Actions\CreateSite;
use SebastianSulinski\LaravelForgeSdk\Actions\CreateDeployment;
use SebastianSulinski\LaravelForgeSdk\Payload\CreateSitePayload;

class DeploySiteJob
{
    public function __construct(
        private CreateSite $createSite,
        private CreateDeployment $createDeployment
    ) {}

    public function handle(): void
    {
        $site = $this->createSite->handle(
            serverId: 123,
            payload: new CreateSitePayload(
                domain: 'example.com',
                projectType: 'php',
                directory: '/public'
            )
        );

        $this->createDeployment->handle(
            serverId: 123,
            siteId: $site->id
        );
    }
}
```

### Using the Forge Facade

For quick operations or in routes/controllers, you can use the static `Forge` facade:

```php
use SebastianSulinski\LaravelForgeSdk\Facades\Forge;
use SebastianSulinski\LaravelForgeSdk\Payload\CreateSitePayload;
use SebastianSulinski\LaravelForgeSdk\Payload\ListServersPayload;
use SebastianSulinski\LaravelForgeSdk\Payload\ListSitesPayload;
use SebastianSulinski\LaravelForgeSdk\Enums\SiteInclude;

// List all servers with filters
$servers = Forge::listServers(
    new ListServersPayload(
        filterProvider: 'digitalocean',
        filterRegion: 'lon1',
        pageSize: 20
    )
);

// Get a specific server
$server = Forge::getServer(serverId: 123);

// List sites with includes
$sites = Forge::listSites(
    payload: new ListSitesPayload(
        filterName: 'example.com',
        include: [SiteInclude::LatestDeployment, SiteInclude::Server]
    )
);

// Get a specific site
$site = Forge::getSite(siteId: 456);

// Create a new site
$site = Forge::createSite(
    serverId: 123,
    payload: new CreateSitePayload(
        domain: 'example.com',
        projectType: 'php',
        directory: '/public'
    )
);

// Trigger deployment
$deployment = Forge::createDeployment(
    serverId: 123,
    siteId: $site->id
);
```

## Working with Relationships and Links

Individual resource objects (like `Server`, `Site`, etc.) include `relationships` and `links` properties that contain metadata from the Forge API:

```php
$server = Forge::getServer(serverId: 123);

// Access relationships - contains links to related resources
$relationships = $server->relationships;
// Example: $relationships['sites']['links']['related']

// Access resource links - contains links related to this resource
$links = $server->links;
// Example: $links['self'] = 'https://forge.laravel.com/api/orgs/xxx/servers/123'
```

List endpoints return a `Response` object that includes pagination metadata and links:

```php
$response = Forge::listServers($payload);

// Access the data as an array
$serversArray = $response->data; // array of Server objects

// Or convert to Collection when needed
$servers = $response->collection(); // Collection<int, Server>

// Access pagination links (array)
$nextUrl = $response->links['next'] ?? null;
$prevUrl = $response->links['prev'] ?? null;
// Or use helper: $nextUrl = $response->link('next');

// Access pagination metadata (array)
$total = $response->meta['total'] ?? null;
$perPage = $response->meta['per_page'] ?? null;
// Or use helper: $total = $response->meta('total');

// Check if response has data/metadata
if ($response->hasData()) {
    // ...
}

if ($response->hasMeta()) {
    // ...
}

// Access included resources
$includedArray = $response->included; // array
$includedCollection = $response->included(); // Collection
```

## Available Actions

The SDK provides actions for all major Forge operations:

### Servers

- `ListServers` - List all servers
- `GetServer` - Get a specific server

### Sites

- `ListSites` - List sites on a server
- `GetSite` - Get a specific site
- `CreateSite` - Create a new site
- `UpdateSite` - Update site details
- `DeleteSite` - Delete a site

### Databases

- `CreateDatabase` - Create a database
- `DeleteDatabaseSchema` - Delete a database schema
- `ListDatabaseUsers` - List database users
- `DeleteDatabaseUser` - Delete a database user

### Deployments

- `ListDeployments` - List deployments for a site
- `CreateDeployment` - Trigger a deployment
- `GetDeploymentStatus` - Get deployment status
- `GetDeploymentScript` - Get deployment script
- `UpdateDeploymentScript` - Update deployment script

### Certificates

- `CreateDomainCertificate` - Create an SSL certificate
- `GetDomainCertificate` - Get certificate details

### Domains

- `ListDomains` - List domains for a site
- `CreateDomain` - Add a domain to a site

### Environment

- `GetEnvContent` - Get environment file content
- `UpdateEnvContent` - Update environment file

### Commands

- `CreateCommand` - Execute a command on the server

### Nginx Templates

- `GetNginxTemplateByName` - Get Nginx template by name

## Response Object

List actions (like `listServers`, `listSites`) return a `Response` object that wraps the data with additional metadata:

**Properties (all arrays for consistency):**
- `data` - `array<int, Resource>` - Array of resources (Servers, Sites, etc.)
- `links` - `array<string, mixed>` - Pagination links (first, last, prev, next)
- `meta` - `array<string, mixed>` - Pagination metadata (current_page, total, per_page, etc.)
- `included` - `array<int, array<string, mixed>>` - Included resources from the API response (if requested)

**Helper methods:**
- `hasData(): bool` - Check if response has data
- `hasLinks(): bool` - Check if response has pagination links
- `hasMeta(): bool` - Check if response has metadata
- `hasIncluded(): bool` - Check if response has included resources
- `link(string $key, mixed $default = null): mixed` - Get a specific link value
- `meta(string $key, mixed $default = null): mixed` - Get a specific meta value
- `collection(): Collection` - Convert data array to a Collection
- `included(): Collection` - Convert included array to a Collection

## Data Objects

The SDK uses typed data objects for all responses:

- `Server` - Server information
- `Site` - Site information (note: does not include serverId as per Forge API v2)
- `Database` - Database information
- `DatabaseUser` - Database user information
- `Certificate` - SSL certificate information
- `Domain` - Domain information
- `Deployment` - Deployment information
- `DeploymentStatus` - Deployment status
- `Repository` - Repository information
- `MaintenanceMode` - Maintenance mode status
- `NginxTemplate` - Nginx template
- `Commit` - Git commit information

All data objects include `relationships` and `links` properties that contain metadata from the Forge API about related resources and resource URLs.

## Enums

Type-safe enums for all Forge constants:

- `CertificateType` - Certificate types
- `CertificateStatus` - Certificate status values
- `SiteStatus` - Site status values
- `SiteType` - Site types
- `DatabaseStatus` - Database status values
- `DeploymentStatus` - Deployment status values
- `PhpVersion` - Available PHP versions
- And more...

## Testing

```bash
composer test
```

The package uses Orchestra Testbench for testing and includes a base `TestCase` class for your tests.

## Contributing

Contributions are welcome! Please feel free to submit a Pull Request. For major changes, please open an issue first to discuss what you would like to change.

Please make sure to update tests as appropriate.

## License

[MIT](LICENSE)
