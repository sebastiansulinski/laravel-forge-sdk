<?php

namespace SebastianSulinski\LaravelForgeSdk\Enums;

enum DatabaseStatus: string
{
    case Installed = 'installed';
    case Installing = 'installing';
    case Removing = 'removing';
}
