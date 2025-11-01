<?php

namespace SebastianSulinski\LaravelForgeSdk\Enums\Deployment;

enum Status: string
{
    case Cancelled = 'cancelled';
    case Deploying = 'deploying';
    case Failed = 'failed';
    case FailedBuild = 'failed-build';
    case Finished = 'finished';
    case Pending = 'pending';
    case Queued = 'queued';
}
