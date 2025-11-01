<?php

namespace SebastianSulinski\LaravelForgeSdk\Traits;

use Carbon\Carbon;
use SebastianSulinski\LaravelForgeSdk\Data\Command;
use SebastianSulinski\LaravelForgeSdk\Enums\Command\Status;

/**
 * @phpstan-type CommandData array{
 *     id: int,
 *     attributes: array{
 *         command: string,
 *         status: string,
 *         user_id?: int|null,
 *         duration?: string|null,
 *         created_at?: string|null,
 *         updated_at?: string|null
 *     }
 * }
 */
trait HasCommand
{
    /**
     * Make a command.
     *
     * @param  CommandData  $data
     */
    protected function makeCommand(array $data): Command
    {
        $attributes = $data['attributes'];

        return new Command(
            id: $data['id'],
            command: $attributes['command'],
            status: Status::from($attributes['status']),
            userId: $attributes['user_id'] ?? null,
            duration: $attributes['duration'] ?? null,
            createdAt: isset($attributes['created_at'])
                ? Carbon::parse($attributes['created_at'])
                : null,
            updatedAt: isset($attributes['updated_at'])
                ? Carbon::parse($attributes['updated_at'])
                : null,
        );
    }
}
