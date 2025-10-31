<?php

namespace SebastianSulinski\LaravelForgeSdk\Traits;

use Carbon\Carbon;
use SebastianSulinski\LaravelForgeSdk\Data\DatabaseUser;
use SebastianSulinski\LaravelForgeSdk\Enums\DatabaseUserStatus;

/**
 * @phpstan-type DatabaseUserData array{
 *     id: int,
 *     attributes: array{
 *         name: string,
 *         status: string,
 *         created_at: string,
 *         updated_at: string
 *     }
 * }
 */
trait HasDatabaseUser
{
    /**
     * Make a database user.
     *
     * @param  DatabaseUserData  $data
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
