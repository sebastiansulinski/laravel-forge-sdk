<?php

namespace SebastianSulinski\LaravelForgeSdk\Data;

use Carbon\Carbon;

readonly class Server
{
    /**
     * Server constructor.
     */
    public function __construct(
        public int $id,
        public string $name,
        public string $provider,
        public string $region,
        public string $ipAddress,
        public string $privateIpAddress,
        public string $phpVersion,
        public string $databaseType,
        public string $connectionStatus,
        public bool $isReady,
        public string $type,
        public Carbon $createdAt,
        public Carbon $updatedAt,
        public ?string $size = null,
        public ?string $ubuntuVersion = null,
        public ?string $phpCliVersion = null,
    ) {}
}
