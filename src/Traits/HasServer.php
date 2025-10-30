<?php

namespace SebastianSulinski\LaravelForgeSdk\Traits;

use SebastianSulinski\LaravelForgeSdk\Data\Server;
use Carbon\Carbon;

trait HasServer
{
    /**
     * Make a server.
     */
    protected function makeServer(array $data): Server
    {
        $attributes = $data['attributes'];

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
            size: $attributes['size'] ?? null,
            ubuntuVersion: $attributes['ubuntu_version'] ?? null,
            phpCliVersion: $attributes['php_cli_version'] ?? null
        );
    }
}
