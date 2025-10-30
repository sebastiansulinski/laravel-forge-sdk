# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

## [0.2.0] - 2025-10-30

### Changed
- **Breaking:** Updated `Forge::updateEnvContent()` method signature to accept `UpdateEnvContentPayload` instead of `string $content` for more flexible environment updates with optional parameters (cache, queues, encryption_key)

## [0.1.0] - 2025-10-30

### Added
- Initial release of unofficial Laravel Forge SDK for API v2
- Type-safe PHP SDK with readonly classes and PHP 8.4/8.5 support
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

[Unreleased]: https://github.com/sebastiansulinski/laravel-forge-sdk/compare/v0.2.0...HEAD
[0.2.0]: https://github.com/sebastiansulinski/laravel-forge-sdk/compare/v0.1.0...v0.2.0
[0.1.0]: https://github.com/sebastiansulinski/laravel-forge-sdk/releases/tag/v0.1.0
