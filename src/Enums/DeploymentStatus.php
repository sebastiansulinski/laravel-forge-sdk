<?php

namespace SebastianSulinski\LaravelForgeSdk\Enums;

enum DeploymentStatus: string
{
    case Cancelled = 'cancelled';
    case Deploying = 'deploying';
    case Failed = 'failed';
    case FailedBuild = 'failed-build';
    case Finished = 'finished';
    case Pending = 'pending';
    case Queued = 'queued';
}
