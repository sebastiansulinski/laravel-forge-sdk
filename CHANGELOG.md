# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

## [0.7.1] - 2025-11-01

### Fixed
- Fixed `Command` data object `duration` property type from `?int` to `?string` to match API response format (returns human-readable format like "5m")

### Changed
- Removed `siteId` property from `Command` data object as it's not included in the API response

## [0.7.0] - 2025-11-01

### Added
- Added `ListCommands` action to fetch site commands with support for filtering and pagination
- Added `WwwRedirectType` enum for managing WWW redirect behavior (`None`, `FromWww`, `ToWww`)
- Added support for wildcard subdomains in site creation with `allow_wildcard_subdomains` parameter
- Added domain mode validations to `CreateSite` action

### Changed
- **Breaking:** Refactored all enums, payloads, and traits to use nested namespaces for better organization
  - Enums moved to `Enums\{Resource}` namespace (e.g., `Enums\Site\Type`, `Enums\Certificate\Status`)
  - Payloads moved to `Payload\{Resource}` namespace (e.g., `Payload\Site\CreatePayload`)
  - Traits moved to appropriate namespaces (e.g., `Data\Concerns\HasApiMetadata`)
- Removed unused classes for cleaner codebase

### Fixed
- Fixed README documentation to correctly show `getSite()` method signature (removed incorrect `serverId` parameter)
- Fixed README examples for `CreateSite` to include required `www_redirect_type` and `allow_wildcard_subdomains` parameters when using `DomainMode::Custom`

## [0.6.3] - 2025-10-31

### Fixed
- Fixed null deployment status handling to default to `Pending` status when API returns null

## [0.6.2] - 2025-10-31

### Changed
- **Breaking:** Renamed `createDatabase()` method to `createDatabaseSchema()` for consistency with related methods
- **Breaking:** Renamed `CreateDatabase` action class to `CreateDatabaseSchema`
- Replaced `Creating` status with `Installing` in `DatabaseStatus` enum to match actual API behavior

### Fixed
- Added missing `Installing` status to `DatabaseStatus` enum (discovered during UAT testing)

## [0.6.1] - 2025-10-31

### Changed
- Refactored `ListDeployments` action to remove `serverId` parameter from method signature for cleaner API
- Removed `serverId` attribute from `Deployment` data object to align with API response structure

## [0.6.0] - 2025-10-31

### Added
- Added `listDatabaseSchemas()` method to retrieve database schemas for a server
- Added `ListDatabaseSchemas` action class
- Added `ListDatabaseSchemasPayload` with support for pagination, sorting, and filtering

## [0.5.1] - 2025-10-31

### Changed
- Added PHP 8.3 support to broaden compatibility (now supports PHP 8.3+)

## [0.5.0] - 2025-10-31

### Added
- Added `ListResponse` data object for handling paginated list endpoints with metadata
- Added `PaginationMode` enum with `All` and `Paginated` modes for flexible pagination control
- Added `HasApiMetadata` trait (in `Data\Concerns`) for convenient dot notation access to API relationships and links
- Added `relationships()` and `links()` accessor methods to `Server` and `Site` data objects
- Added `hasRelationship()` and `hasLink()` helper methods for checking existence
- Added comprehensive pagination documentation and examples to README

### Changed
- **Breaking:** All list methods now return `ListResponse` instead of `Collection`
  - Use `->collection()` method on `ListResponse` to get a Collection instance
  - Example: `Forge::listServers($payload)->collection()`
- **Breaking:** List payloads now use `mode` property with `PaginationMode` enum instead of boolean `fetchAll`
  - `PaginationMode::Paginated` (default) - returns single page
  - `PaginationMode::All` - fetches all pages automatically
- Refactored all list actions to use native PHP `array_map()` for better performance
- Refactored response handling to use `ParsesResponse` trait for consistency
- Moved `HasApiMetadata` trait to `Data\Concerns` namespace for better architectural alignment
- Enhanced `Server` and `Site` data objects with `relationships` and `links` properties

### Improved
- Enhanced type safety with explicit PHPStan type definitions across all actions
- Improved documentation with detailed pagination mode examples
- Added comprehensive tests for relationship and link accessor methods

## [0.4.0] - 2025-10-30

### Added
- Added `listDatabaseUsers()` method to retrieve database users for a server
- Added `listDeployments()` method to retrieve deployment history for a site
- Added `listDomains()` method to retrieve domains for a site

## [0.3.0] - 2025-10-30

### Added
- Added `getNginxTemplateByName()` method to retrieve specific nginx templates by name

## [0.2.0] - 2025-10-30

### Changed
- **Breaking:** Updated `Forge::updateEnvContent()` method signature to accept `UpdateEnvContentPayload` instead of `string $content` for more flexible environment updates with optional parameters (cache, queues, encryption_key)

## [0.1.0] - 2025-10-30

### Added
- Initial release of unofficial Laravel Forge SDK for API v2
- Type-safe PHP SDK with readonly classes and PHP 8.3+ support
- Server management actions (list, get)
- Site management actions (create, get, list, update, delete)
- Deployment actions (create, get status, get/update script)
- Database actions (create, delete schema, delete user, list users)
- Domain management (create, list)
- SSL certificate management (create, get for domains)
- Environment management (get, update content)
- Command execution on sites
- Nginx template retrieval
- Fluent Forge service class for chained operations
- Laravel Facade support
- Comprehensive test suite with Pest
- PHPStan level 9 static analysis
- Deptrac architectural analysis
- Pre-commit hooks for code quality
- Full PHPDoc annotations and type hints

### Notes
- **This is an unofficial SDK** - Laravel Forge's official SDK currently only supports API v1
- This is a beta release as Laravel Forge API v2 documentation is still evolving
- Additional actions will be added as the Forge API documentation is updated
- Breaking changes may occur in 0.x versions before 1.0.0 stable release

[Unreleased]: https://github.com/sebastiansulinski/laravel-forge-sdk/compare/v0.7.1...HEAD
[0.7.1]: https://github.com/sebastiansulinski/laravel-forge-sdk/compare/v0.7.0...v0.7.1
[0.7.0]: https://github.com/sebastiansulinski/laravel-forge-sdk/compare/v0.6.3...v0.7.0
[0.6.3]: https://github.com/sebastiansulinski/laravel-forge-sdk/compare/v0.6.2...v0.6.3
[0.6.2]: https://github.com/sebastiansulinski/laravel-forge-sdk/compare/v0.6.1...v0.6.2
[0.6.1]: https://github.com/sebastiansulinski/laravel-forge-sdk/compare/v0.6.0...v0.6.1
[0.6.0]: https://github.com/sebastiansulinski/laravel-forge-sdk/compare/v0.5.1...v0.6.0
[0.5.1]: https://github.com/sebastiansulinski/laravel-forge-sdk/compare/v0.5.0...v0.5.1
[0.5.0]: https://github.com/sebastiansulinski/laravel-forge-sdk/compare/v0.4.0...v0.5.0
[0.4.0]: https://github.com/sebastiansulinski/laravel-forge-sdk/compare/v0.3.0...v0.4.0
[0.3.0]: https://github.com/sebastiansulinski/laravel-forge-sdk/compare/v0.2.0...v0.3.0
[0.2.0]: https://github.com/sebastiansulinski/laravel-forge-sdk/compare/v0.1.0...v0.2.0
[0.1.0]: https://github.com/sebastiansulinski/laravel-forge-sdk/releases/tag/v0.1.0
