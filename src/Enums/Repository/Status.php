<?php

namespace SebastianSulinski\LaravelForgeSdk\Enums\Repository;

enum Status: string
{
    case Installed = 'installed';
    case Installing = 'installing';
    case Removing = 'removing';
}
