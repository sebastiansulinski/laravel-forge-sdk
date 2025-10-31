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
use SebastianSulinski\LaravelForgeSdk\Enums\PaginationMode;
use SebastianSulinski\LaravelForgeSdk\Enums\SiteInclude;
use SebastianSulinski\LaravelForgeSdk\Payload\CreateSitePayload;
use SebastianSulinski\LaravelForgeSdk\Payload\ListServersPayload;
use SebastianSulinski\LaravelForgeSdk\Payload\ListSitesPayload;

class DeploySiteJob
{
    public function __construct(private Forge $forge) {}

    public function handle(): void
    {
        // List servers with pagination (default mode)
        $response = $this->forge->listServers(
            new ListServersPayload(
                filterProvider: 'digitalocean',
                filterRegion: 'lon1',
                pageSize: 20
            )
        );

        // Access data as Collection
        $servers = $response->collection();

        // List all servers without pagination (fetches all pages automatically)
        $allServersResponse = $this->forge->listServers(
            new ListServersPayload(
                mode: PaginationMode::All,
                filterProvider: 'digitalocean'
            )
        );

        // Iterate over all servers
        foreach ($allServersResponse->collection() as $server) {
            // Process each server...
        }

        // Get a specific server
        $server = $this->forge->getServer(serverId: 123);
        // Access relationships and links
        $siteRelationships = $server->relationships['sites'] ?? null;
        $selfLink = $server->links['self'] ?? null;

        // List sites with includes
        $sitesResponse = $this->forge->listSites(
            serverId: 123,
            payload: new ListSitesPayload(
                filterName: 'example.com',
                include: [SiteInclude::LatestDeployment, SiteInclude::Server]
            )
        );

        // Get a specific site
        $site = $this->forge->getSite(serverId: 123, siteId: 456);

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
use SebastianSulinski\LaravelForgeSdk\Enums\PaginationMode;
use SebastianSulinski\LaravelForgeSdk\Enums\SiteInclude;
use SebastianSulinski\LaravelForgeSdk\Payload\CreateSitePayload;
use SebastianSulinski\LaravelForgeSdk\Payload\ListServersPayload;
use SebastianSulinski\LaravelForgeSdk\Payload\ListSitesPayload;

// List servers with pagination (default) - returns ListResponse
$response = Forge::listServers(
    new ListServersPayload(
        filterProvider: 'digitalocean',
        filterRegion: 'lon1',
        pageSize: 20
    )
);

// Access data as Collection
$servers = $response->collection();

// List all servers (fetches all pages automatically)
$allServersResponse = Forge::listServers(
    new ListServersPayload(
        mode: PaginationMode::All,
        filterProvider: 'digitalocean'
    )
);

// Get a specific server
$server = Forge::getServer(serverId: 123);

// List sites with includes
$sitesResponse = Forge::listSites(
    serverId: 123,
    payload: new ListSitesPayload(
        filterName: 'example.com',
        include: [SiteInclude::LatestDeployment, SiteInclude::Server]
    )
);

// Get a specific site
$site = Forge::getSite(serverId: 123, siteId: 456);

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

## Working with Pagination

All list endpoints return a `ListResponse` object that provides flexible pagination options:

### Paginated Mode (Default)

Returns a single page of results:

```php
use SebastianSulinski\LaravelForgeSdk\Enums\PaginationMode;
use SebastianSulinski\LaravelForgeSdk\Payload\ListServersPayload;

// Default behavior - returns first page with 20 items
$response = Forge::listServers(
    new ListServersPayload(
        pageSize: 20
    )
);

// Access data as Collection
$servers = $response->collection();

// Access pagination metadata
$nextCursor = $response->meta('next_cursor'); // For cursor-based pagination
$total = $response->meta('total');
$perPage = $response->meta('per_page');

// Access pagination links
$nextUrl = $response->link('next');
$prevUrl = $response->link('prev');

// Check if there are more pages
if ($response->hasMeta() && $response->meta('next_cursor')) {
    // Fetch next page
    $nextPage = Forge::listServers(
        new ListServersPayload(
            pageCursor: $response->meta('next_cursor'),
            pageSize: 20
        )
    );
}
```

### Fetch All Mode

Automatically fetches all pages and returns all results:

```php
// Fetch all servers across all pages
$response = Forge::listServers(
    new ListServersPayload(
        mode: PaginationMode::All,
        filterProvider: 'digitalocean'
    )
);

// All servers from all pages
$allServers = $response->collection();

// The response still contains metadata from the last page
$total = $response->meta('total');
```

## Working with Relationships and Links

Individual resource objects (like `Server`, `Site`, etc.) include `relationships` and `links` properties that contain metadata from the Forge API. These resources use the `HasApiMetadata` trait which provides convenient accessor methods with dot notation support.

### Direct Property Access

```php
$server = Forge::getServer(serverId: 123);

// Access relationships - contains links to related resources
$relationships = $server->relationships;
// Example: $relationships['sites']['links']['related']

// Access resource links - contains links related to this resource
$links = $server->links;
// Example: $links['self'] = 'https://forge.laravel.com/api/orgs/xxx/servers/123'
```

### Using Accessor Methods with Dot Notation

For easier access to nested values, use the provided accessor methods:

```php
$server = Forge::getServer(serverId: 123);
$site = Forge::getSite(serverId: 123, siteId: 456);

// Access nested relationships using dot notation
$sitesRelatedLink = $server->relationships('sites.links.related');
$serverDataId = $site->relationships('server.data.id');
$tagType = $site->relationships('tags.data.0.type');

// Access nested links using dot notation
$selfHref = $server->links('self.href');
$relatedUrl = $site->links('server.related');

// Provide default values
$tagType = $site->relationships('tags.data.0.type', 'default-type');
$customLink = $server->links('custom.link', 'https://fallback.url');

// Check if relationships or links exist
if ($server->hasRelationship('sites')) {
    $sitesData = $server->relationships('sites');
}

if ($site->hasLink('self')) {
    $selfLink = $site->links('self');
}

// Combine with null coalescing for cleaner code
$relatedSites = $server->relationships('sites.data') ?? [];
$selfUrl = $site->links('self.href') ?? 'N/A';
```

**Available accessor methods:**
- `relationships(string $key, mixed $default = null): mixed` - Get relationship value with dot notation
- `links(string $key, mixed $default = null): mixed` - Get link value with dot notation
- `hasRelationship(string $key): bool` - Check if a relationship exists
- `hasLink(string $key): bool` - Check if a link exists

## Working with ListResponse

List endpoints return a `ListResponse` object that includes pagination metadata and links:

```php
$response = Forge::listServers($payload);

// Access the data as an array
$serversArray = $response->data; // array of Server objects

// Convert to Collection (recommended)
$servers = $response->collection(); // Collection<int, Server>

// Iterate over results
foreach ($response->collection() as $server) {
    echo $server->name;
}

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
    // Process data...
}

if ($response->hasMeta()) {
    // Check pagination info...
}

// Access included resources (when using include parameter)
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

## ListResponse Object

List actions (like `listServers`, `listSites`) return a `ListResponse` object that wraps the data with additional metadata:

**Properties (all public arrays):**
- `data` - `array<int, Resource>` - Array of resources (Servers, Sites, etc.)
- `links` - `array<string, mixed>` - Pagination links (first, last, prev, next)
- `meta` - `array<string, mixed>` - Pagination metadata (current_page, total, per_page, next_cursor, etc.)
- `included` - `array<int, array<string, mixed>>` - Included resources from the API response (if requested)

**Helper methods:**
- `hasData(): bool` - Check if response has data
- `hasLinks(): bool` - Check if response has pagination links
- `hasMeta(): bool` - Check if response has metadata
- `hasIncluded(): bool` - Check if response has included resources
- `link(string $key, mixed $default = null): mixed` - Get a specific link value
- `meta(string $key, mixed $default = null): mixed` - Get a specific meta value
- `collection(): Collection` - Convert data array to a Collection (recommended for iteration)
- `included(): Collection` - Convert included array to a Collection

**Pagination Modes:**

All list payloads support a `mode` parameter to control pagination behavior:
- `PaginationMode::Paginated` (default) - Returns a single page of results
- `PaginationMode::All` - Automatically fetches all pages and returns all results

## Data Objects

The SDK uses typed data objects for all responses:

### Resource Objects
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

**Resources with API Metadata:**

The `Server` and `Site` data objects include `relationships` and `links` properties that contain metadata from the Forge API about related resources and resource URLs. These objects use the `HasApiMetadata` trait which provides convenient accessor methods:

- `relationships(string $key, mixed $default = null)` - Access nested relationship data with dot notation
- `links(string $key, mixed $default = null)` - Access nested link data with dot notation
- `hasRelationship(string $key)` - Check if a relationship exists
- `hasLink(string $key)` - Check if a link exists

See the "Working with Relationships and Links" section for usage examples.

### Response Objects
- `ListResponse` - Wrapper for list endpoints containing data array, pagination links, metadata, and included resources

## Enums

Type-safe enums for all Forge constants:

### API Behavior
- `PaginationMode` - Pagination modes (`All`, `Paginated`)

### Resource Status & Types
- `CertificateType` - Certificate types
- `CertificateStatus` - Certificate status values
- `SiteStatus` - Site status values
- `SiteType` - Site types
- `DatabaseStatus` - Database status values
- `DeploymentStatus` - Deployment status values
- `PhpVersion` - Available PHP versions
- And more...

## Traits

The SDK provides reusable traits for common functionality:

### HasApiMetadata

Used by `Server` and `Site` data objects to provide convenient access to API metadata:

```php
use SebastianSulinski\LaravelForgeSdk\Data\Concerns\HasApiMetadata;

readonly class YourDataObject
{
    use HasApiMetadata;

    public function __construct(
        public array $relationships = [],
        public array $links = [],
        // ... other properties
    ) {}
}
```

**Methods:**
- `relationships(string $key, mixed $default = null): mixed` - Access relationships with dot notation
- `links(string $key, mixed $default = null): mixed` - Access links with dot notation
- `hasRelationship(string $key): bool` - Check if relationship exists
- `hasLink(string $key): bool` - Check if link exists

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
