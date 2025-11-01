<?php

namespace SebastianSulinski\LaravelForgeSdk\Enums\Database;

enum Status: string
{
    case Installed = 'installed';
    case Installing = 'installing';
    case Removing = 'removing';
}
