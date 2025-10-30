<?php

namespace SebastianSulinski\LaravelForgeSdk\Enums;

enum SiteStatus: string
{
    case Installed = 'installed';
    case Creating = 'creating';
    case Removing = 'removing';
    case Installing = 'installing';
    case Uninstalling = 'uninstalling';
    case Deployed = 'deployed';
    case NeverDeployed = 'never-deployed';
    case Unhealthy = 'unhealthy';
    case Deploying = 'deploying';
    case Failed = 'failed';
    case Maintenance = 'maintenance';
}
