<?php

namespace SebastianSulinski\LaravelForgeSdk\Traits;

use Carbon\Carbon;
use SebastianSulinski\LaravelForgeSdk\Data\Server;

/**
 * @phpstan-type ServerData array{
 *     id: int,
 *     attributes: array{
 *         name: string,
 *         provider: string,
 *         region: string,
 *         ip_address: string,
 *         private_ip_address: string,
 *         php_version: string,
 *         database_type: string,
 *         connection_status: string,
 *         is_ready: bool,
 *         type: string,
 *         created_at: string,
 *         updated_at: string,
 *         size?: string|null,
 *         ubuntu_version?: string|null,
 *         php_cli_version?: string|null
 *     },
 *     relationships?: array<string, mixed>,
 *     links?: array<string, mixed>
 * }
 */
trait HasServer
{
    /**
     * Make a server.
     *
     * @param  ServerData  $data
     */
    protected function makeServer(array $data): Server
    {
        $attributes = $data['attributes'];
        $relationships = $data['relationships'] ?? [];
        $links = $data['links'] ?? [];

        return new Server(
            id: $data['id'],
            name: $attributes['name'],
            provider: $attributes['provider'],
            region: $attributes['region'],
            ipAddress: $attributes['ip_address'],
            privateIpAddress: $attributes['private_ip_address'],
            phpVersion: $attributes['php_version'],
            databaseType: $attributes['database_type'],
            connectionStatus: $attributes['connection_status'],
            isReady: $attributes['is_ready'],
            type: $attributes['type'],
            createdAt: Carbon::parse($attributes['created_at']),
            updatedAt: Carbon::parse($attributes['updated_at']),
            relationships: $relationships,
            links: $links,
            size: $attributes['size'] ?? null,
            ubuntuVersion: $attributes['ubuntu_version'] ?? null,
            phpCliVersion: $attributes['php_cli_version'] ?? null
        );
    }
}
