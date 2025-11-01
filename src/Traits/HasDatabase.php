<?php

namespace SebastianSulinski\LaravelForgeSdk\Traits;

use Carbon\Carbon;
use SebastianSulinski\LaravelForgeSdk\Data\Database;
use SebastianSulinski\LaravelForgeSdk\Enums\Database\Status;

/**
 * @phpstan-type DatabaseData array{
 *     id: int,
 *     attributes: array{
 *         name: string,
 *         status: string,
 *         created_at?: string|null,
 *         updated_at?: string|null
 *     }
 * }
 */
trait HasDatabase
{
    /**
     * Make a database.
     *
     * @param  DatabaseData  $data
     */
    protected function makeDatabase(int $serverId, array $data): Database
    {
        $attributes = $data['attributes'];

        return new Database(
            id: $data['id'],
            serverId: $serverId,
            name: $attributes['name'],
            status: Status::from($attributes['status']),
            createdAt: isset($attributes['created_at'])
                ? Carbon::parse($attributes['created_at'])
                : null,
            updatedAt: isset($attributes['updated_at'])
                ? Carbon::parse($attributes['updated_at'])
                : null,
        );
    }
}
