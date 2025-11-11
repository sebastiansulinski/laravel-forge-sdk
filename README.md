# Laravel Forge API V2 SDK

An unofficial, type-safe Laravel package for interacting with the Laravel Forge API V2.

> **Note:** This is an **unofficial SDK**. Laravel Forge's official SDK currently only supports API v1. This package is a work in progress and additional actions will be added over time as the Forge API v2 documentation gets updated.

## Requirements

- PHP 8.3+
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
use SebastianSulinski\LaravelForgeSdk\Enums\Repository\Provider;
use SebastianSulinski\LaravelForgeSdk\Enums\Server\PhpVersion;
use SebastianSulinski\LaravelForgeSdk\Enums\Site\DomainMode;
use SebastianSulinski\LaravelForgeSdk\Enums\Site\IncludeOption;
use SebastianSulinski\LaravelForgeSdk\Enums\Site\Type;
use SebastianSulinski\LaravelForgeSdk\Enums\Site\WwwRedirectType;
use SebastianSulinski\LaravelForgeSdk\Payload\Site\CreatePayload;
use SebastianSulinski\LaravelForgeSdk\Payload\Server\ListPayload as ListServersPayload;
use SebastianSulinski\LaravelForgeSdk\Payload\Site\ListPayload as ListSitesPayload;

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
        // Check and access relationships and links
        if ($server->hasRelationships()) {
            $siteRelationships = $server->relationships('sites.links.related');
        }
        if ($server->hasLinks()) {
            $selfLink = $server->links('self');
        }

        // List sites with includes
        $sitesResponse = $this->forge->listSites(
            serverId: 123,
            payload: new ListSitesPayload(
                filterName: 'example.com',
                include: [IncludeOption::LatestDeployment, IncludeOption::Server]
            )
        );

        // Get a specific site
        $site = $this->forge->getSite(siteId: 456);

        // Create a new site
        $site = $this->forge->createSite(
            serverId: 123,
            payload: new CreatePayload(
                type: Type::Laravel,
                name: 'example.com',
                domain_mode: DomainMode::Custom,
                www_redirect_type: WwwRedirectType::None, // Required when domain_mode is Custom
                allow_wildcard_subdomains: false,         // Required when domain_mode is Custom
                web_directory: '/public',
                php_version: PhpVersion::Php84,
                source_control_provider: Provider::Github,
                repository: 'username/repo',
                branch: 'main',
                install_composer_dependencies: true,
                push_to_deploy: false
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
use SebastianSulinski\LaravelForgeSdk\Enums\Repository\Provider;
use SebastianSulinski\LaravelForgeSdk\Enums\Server\PhpVersion;
use SebastianSulinski\LaravelForgeSdk\Enums\Site\DomainMode;
use SebastianSulinski\LaravelForgeSdk\Enums\Site\Type;
use SebastianSulinski\LaravelForgeSdk\Enums\Site\WwwRedirectType;
use SebastianSulinski\LaravelForgeSdk\Payload\Site\CreatePayload;

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
            payload: new CreatePayload(
                type: Type::Laravel,
                name: 'example.com',
                domain_mode: DomainMode::Custom,
                www_redirect_type: WwwRedirectType::None, // Required when domain_mode is Custom
                allow_wildcard_subdomains: false,         // Required when domain_mode is Custom
                web_directory: '/public',
                php_version: PhpVersion::Php84,
                source_control_provider: Provider::Github,
                repository: 'username/repo',
                branch: 'main',
                install_composer_dependencies: true,
                push_to_deploy: false
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
use SebastianSulinski\LaravelForgeSdk\Enums\Repository\Provider;
use SebastianSulinski\LaravelForgeSdk\Enums\Server\PhpVersion;
use SebastianSulinski\LaravelForgeSdk\Enums\Site\DomainMode;
use SebastianSulinski\LaravelForgeSdk\Enums\Site\IncludeOption;
use SebastianSulinski\LaravelForgeSdk\Enums\Site\Type;
use SebastianSulinski\LaravelForgeSdk\Enums\Site\WwwRedirectType;
use SebastianSulinski\LaravelForgeSdk\Payload\Site\CreatePayload;
use SebastianSulinski\LaravelForgeSdk\Payload\Server\ListPayload as ListServersPayload;
use SebastianSulinski\LaravelForgeSdk\Payload\Site\ListPayload as ListSitesPayload;

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
        include: [IncludeOption::LatestDeployment, IncludeOption::Server]
    )
);

// Get a specific site
$site = Forge::getSite(siteId: 456);

// Create a new site
$site = Forge::createSite(
    serverId: 123,
    payload: new CreatePayload(
        type: Type::Laravel,
        name: 'example.com',
        domain_mode: DomainMode::Custom,
        www_redirect_type: WwwRedirectType::None, // Required when domain_mode is Custom
        allow_wildcard_subdomains: false,         // Required when domain_mode is Custom
        web_directory: '/public',
        php_version: PhpVersion::Php84,
        source_control_provider: Provider::Github,
        repository: 'username/repo',
        branch: 'main',
        install_composer_dependencies: true,
        push_to_deploy: false
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
use SebastianSulinski\LaravelForgeSdk\Payload\Server\ListPayload as ListServersPayload;

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
$site = Forge::getSite(siteId: 456);

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

// Check if any relationships or links exist
if ($server->hasRelationships()) {
    // Server has relationships available
    $sitesData = $server->relationships('sites');
}

if ($site->hasLinks()) {
    // Site has links available
    $selfLink = $site->links('self');
}

// Check if a specific relationship or link exists
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
- `hasRelationship(string $key): bool` - Check if a specific relationship exists
- `hasLink(string $key): bool` - Check if a specific link exists
- `hasRelationships(): bool` - Check if any relationships exist
- `hasLinks(): bool` - Check if any links exist

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

- `CreateDatabaseSchema` - Create a database schema
- `ListDatabaseSchemas` - List database schemas
- `DeleteDatabaseSchema` - Delete a database schema
- `CreateDatabaseUser` - Create a database user
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
- `DeleteDomainCertificate` - Delete a domain certificate

### Domains

- `ListDomains` - List domains for a site
- `GetDomain` - Get a specific domain
- `CreateDomain` - Add a domain to a site

### Environment

- `GetEnvContent` - Get environment file content
- `UpdateEnvContent` - Update environment file

### Commands

- `ListCommands` - List commands executed on a site
- `CreateCommand` - Execute a command on the server

### Nginx

- `GetNginxTemplateByName` - Get Nginx template by name
- `GetNginxConfiguration` - Get Nginx configuration for a site

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
- `Command` - Command information
- `Repository` - Repository information
- `MaintenanceMode` - Maintenance mode status
- `NginxTemplate` - Nginx template
- `NginxConfiguration` - Nginx configuration
- `Commit` - Git commit information

**Resources with API Metadata:**

The `Server` and `Site` data objects include `relationships` and `links` properties that contain metadata from the Forge API about related resources and resource URLs. These objects use the `HasApiMetadata` trait which provides convenient accessor methods:

- `relationships(string $key, mixed $default = null)` - Access nested relationship data with dot notation
- `links(string $key, mixed $default = null)` - Access nested link data with dot notation
- `hasRelationship(string $key)` - Check if a specific relationship exists
- `hasLink(string $key)` - Check if a specific link exists
- `hasRelationships()` - Check if any relationships exist
- `hasLinks()` - Check if any links exist

See the "Working with Relationships and Links" section for usage examples.

### Response Objects
- `ListResponse` - Wrapper for list endpoints containing data array, pagination links, metadata, and included resources

## Enums

Type-safe enums for all Forge constants:

### API Behavior
- `PaginationMode` - Pagination modes (`All`, `Paginated`)

### Site
- `Site\Type` - Site types (`Laravel`, `Symfony`, `Statamic`, `Wordpress`, `PhpMyAdmin`, `Php`, `NextJs`, `NuxtJs`, `StaticHtml`, `Other`, `Custom`)
- `Site\Status` - Site status values
- `Site\IncludeOption` - Available include options for site queries
- `Site\DomainMode` - Domain configuration mode (`OnForge`, `Custom`)
- `Site\WwwRedirectType` - WWW redirect behavior (`None`, `FromWww`, `ToWww`)
- `Site\MaintenanceModeStatus` - Maintenance mode status values

### Certificate
- `Certificate\Type` - Certificate types (`LetsEncrypt`, `Csr`, `Existing`)
- `Certificate\Status` - Certificate status values
- `Certificate\KeyType` - Certificate key types (`Ecdsa`, `Rsa`)
- `Certificate\VerificationMethod` - Verification methods (`Http01`, `Dns01`)
- `Certificate\RequestStatus` - Certificate request status values

### Command
- `Command\Status` - Command status values (`Waiting`, `Running`, `Finished`, `Timeout`, `Failed`)

### Database
- `Database\Status` - Database status values
- `Database\UserStatus` - Database user status values

### Deployment
- `Deployment\Status` - Deployment status values

### Domain
- `Domain\Type` - Domain types (`Primary`, `Alias`)
- `Domain\Status` - Domain status values

### Repository
- `Repository\Provider` - Version control providers (`Github`, `Gitlab`, `Bitbucket`, `GitlabCustom`, `Custom`)
- `Repository\Status` - Repository status values

### Server
- `Server\PhpVersion` - Available PHP versions (`Php56` through `Php85`)

## Payloads

Payload classes represent request data for API operations. They are organized by resource type and provide type-safe interfaces for constructing API requests.

### Site Payloads
- `Site\CreatePayload` - Data for creating a new site
  - Required: `type` (Type enum)
  - Optional: 28+ properties including `domain_mode`, `name`, `web_directory`, `php_version`, `source_control_provider`, `repository`, `branch`, `install_composer_dependencies`, `push_to_deploy`, and more
  - See [Forge API docs](https://forge.laravel.com/docs/api-reference/sites/create-site) for all available properties
- `Site\UpdatePayload` - Data for updating site properties
- `Site\ListPayload` - Query parameters for listing sites
  - Supports pagination, filtering by name/status, and includes (via `IncludeOption` enum)

### Certificate Payloads
- `Certificate\CreatePayload` - Abstract base class for certificate creation
- `Certificate\CreateLetsEncryptPayload` - Create Let's Encrypt certificate
- `Certificate\CreateCsrPayload` - Create certificate from CSR
- `Certificate\CreateExistingPayload` - Install existing certificate

### Database Payloads
- `Database\CreateSchemaPayload` - Data for creating a database schema
- `Database\CreateUserPayload` - Data for creating a database user
- `Database\ListSchemasPayload` - Query parameters for listing database schemas
- `Database\ListUsersPayload` - Query parameters for listing database users

### Deployment Payloads
- `Deployment\ListPayload` - Query parameters for listing deployments
- `Deployment\UpdateScriptPayload` - Data for updating deployment script

### Domain Payloads
- `Domain\CreatePayload` - Data for adding a domain to a site

### Command Payloads
- `Command\ListPayload` - Query parameters for listing commands
  - Supports filtering by user ID, status, command text, and pagination

### Server Payloads
- `Server\ListPayload` - Query parameters for listing servers
  - Supports filtering by provider, region, and pagination
- `Server\CreateCommandPayload` - Data for executing server commands

### Environment Payloads
- `Env\UpdatePayload` - Data for updating environment file content

### Pagination
- `PaginationParameters` - Reusable pagination parameters used by all List payloads
  - Properties: `sort`, `pageSize`, `pageCursor`

### Usage Examples

```php
use SebastianSulinski\LaravelForgeSdk\Payload\Site\CreatePayload;
use SebastianSulinski\LaravelForgeSdk\Payload\Site\UpdatePayload;
use SebastianSulinski\LaravelForgeSdk\Payload\Site\ListPayload;
use SebastianSulinski\LaravelForgeSdk\Payload\Database\CreateSchemaPayload;
use SebastianSulinski\LaravelForgeSdk\Payload\Database\CreateUserPayload;
use SebastianSulinski\LaravelForgeSdk\Payload\Certificate\CreateLetsEncryptPayload;
use SebastianSulinski\LaravelForgeSdk\Enums\Site\Type;
use SebastianSulinski\LaravelForgeSdk\Enums\Site\DomainMode;
use SebastianSulinski\LaravelForgeSdk\Enums\Site\IncludeOption;
use SebastianSulinski\LaravelForgeSdk\Enums\Site\WwwRedirectType;
use SebastianSulinski\LaravelForgeSdk\Enums\Server\PhpVersion;
use SebastianSulinski\LaravelForgeSdk\Enums\Repository\Provider;
use SebastianSulinski\LaravelForgeSdk\Enums\Certificate\Type as CertificateType;

// Create a site with all common options
$createSitePayload = new CreatePayload(
    type: Type::Laravel,
    name: 'example.com',
    domain_mode: DomainMode::Custom,
    www_redirect_type: WwwRedirectType::None, // Required when domain_mode is Custom
    allow_wildcard_subdomains: false,         // Required when domain_mode is Custom
    web_directory: '/public',
    php_version: PhpVersion::Php84,
    source_control_provider: Provider::Github,
    repository: 'username/repo',
    branch: 'main',
    install_composer_dependencies: true,
    push_to_deploy: false,
    zero_downtime_deployments: true
);

// Update site properties (all properties are optional)
$updateSitePayload = new UpdatePayload(
    php_version: PhpVersion::Php85,
    push_to_deploy: true
);

// List sites with filters and includes
$listSitesPayload = new ListPayload(
    filterName: 'example.com',
    include: [IncludeOption::LatestDeployment, IncludeOption::Server],
    pageSize: 50
);

// Create a database schema
$createDatabasePayload = new CreateSchemaPayload(
    name: 'my_database',
    user: 'db_user',
    password: 'secure_password'
);

// Create a database user
$createDatabaseUserPayload = new CreateUserPayload(
    name: 'app_user',
    password: 'secure_password',
    readOnly: false,
    databaseIds: [456, 457]
);

// Create Let's Encrypt certificate
$createCertificatePayload = new CreateLetsEncryptPayload(
    type: CertificateType::LetsEncrypt,
    domains: ['example.com', 'www.example.com']
);
```

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
- `hasRelationship(string $key): bool` - Check if a specific relationship exists
- `hasLink(string $key): bool` - Check if a specific link exists
- `hasRelationships(): bool` - Check if any relationships exist
- `hasLinks(): bool` - Check if any links exist

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
