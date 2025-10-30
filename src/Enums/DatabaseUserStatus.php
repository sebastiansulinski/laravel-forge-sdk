<?php

namespace SebastianSulinski\LaravelForgeSdk\Enums;

enum DatabaseUserStatus: string
{
    case Installed = 'installed';
    case Updating = 'updating';
    case Installing = 'installing';
    case Removing = 'removing';
}
