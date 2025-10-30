<?php

namespace SebastianSulinski\LaravelForgeSdk\Traits;

use Carbon\Carbon;
use SebastianSulinski\LaravelForgeSdk\Data\Database;
use SebastianSulinski\LaravelForgeSdk\Enums\DatabaseStatus;

trait HasDatabase
{
    /**
     * Make a database.
     */
    protected function makeDatabase(int $serverId, array $data): Database
    {
        $attributes = $data['attributes'];

        return new Database(
            id: $data['id'],
            serverId: $serverId,
            name: $attributes['name'],
            status: DatabaseStatus::from($attributes['status']),
            createdAt: isset($attributes['created_at'])
                ? Carbon::parse($attributes['created_at'])
                : null,
            updatedAt: isset($attributes['updated_at'])
                ? Carbon::parse($attributes['updated_at'])
                : null,
        );
    }
}
