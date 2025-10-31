<?php

namespace SebastianSulinski\LaravelForgeSdk\Enums;

enum SiteInclude: string
{
    case Server = 'server';
    case Tags = 'tags';
    case TagsCount = 'tagsCount';
    case TagsExists = 'tagsExists';
    case LatestDeployment = 'latestDeployment';
    case LatestDeploymentCount = 'latestDeploymentCount';
    case LatestDeploymentExists = 'latestDeploymentExists';
}
