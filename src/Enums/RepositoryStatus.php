<?php

namespace SebastianSulinski\LaravelForgeSdk\Enums;

enum RepositoryStatus: string
{
    case Installed = 'installed';
    case Installing = 'installing';
    case Removing = 'removing';
}
