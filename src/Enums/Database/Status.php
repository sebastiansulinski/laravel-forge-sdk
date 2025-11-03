<?php

namespace SebastianSulinski\LaravelForgeSdk\Enums\Database;

enum Status: string
{
    case Deleting = 'deleting';
    case Disabled = 'disabled';
    case Disabling = 'disabling';
    case Enabling = 'enabling';
    case Failed = 'failed';
    case FailedRunner = 'failed-runner';
    case FailedUnknown = 'failed-unknown';
    case Installed = 'installed';
    case Installing = 'installing';
    case Removing = 'removing';
    case Restarting = 'restarting';
    case Restoring = 'restoring';
    case Running = 'running';
    case Starting = 'starting';
    case Stopped = 'stopped';
    case Stopping = 'stopping';
    case Success = 'success';
    case Syncing = 'syncing';
    case Updating = 'updating';
}
