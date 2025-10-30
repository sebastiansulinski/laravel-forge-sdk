<?php

namespace SebastianSulinski\LaravelForgeSdk\Traits;

use SebastianSulinski\LaravelForgeSdk\Data\DatabaseUser;
use SebastianSulinski\LaravelForgeSdk\Enums\DatabaseUserStatus;
use Carbon\Carbon;

trait HasDatabaseUser
{
    /**
     * Make a database user.
     */
    protected function makeDatabaseUser(int $serverId, array $data): DatabaseUser
    {
        $attributes = $data['attributes'];

        return new DatabaseUser(
            id: $data['id'],
            serverId: $serverId,
            name: $attributes['name'],
            status: DatabaseUserStatus::from($attributes['status']),
            createdAt: Carbon::parse($attributes['created_at']),
            updatedAt: Carbon::parse($attributes['updated_at'])
        );
    }
}
