<?php

namespace SebastianSulinski\LaravelForgeSdk\Enums\Database;

enum UserStatus: string
{
    case Installed = 'installed';
    case Updating = 'updating';
    case Installing = 'installing';
    case Removing = 'removing';
}
