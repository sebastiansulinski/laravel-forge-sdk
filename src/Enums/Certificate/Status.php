<?php

namespace SebastianSulinski\LaravelForgeSdk\Enums\Certificate;

enum Status: string
{
    case Installing = 'installing';
    case Installed = 'installed';
    case Removing = 'removing';
    case Restarting = 'restarting';
    case Stopping = 'stopping';
    case Stopped = 'stopped';
    case Starting = 'starting';
    case Syncing = 'syncing';
    case Updating = 'updating';
    case Disabling = 'disabling';
    case Disabled = 'disabled';
    case Enabling = 'enabling';
    case Running = 'running';
    case Restoring = 'restoring';
    case Deleting = 'deleting';
    case Failed = 'failed';
    case Success = 'success';
    case FailedUnknown = 'failed-unknown';
    case FailedRunner = 'failed-runner';
}
