<?php

namespace SebastianSulinski\LaravelForgeSdk\Enums;

enum DatabaseStatus: string
{
    case Installed = 'installed';
    case Creating = 'creating';
    case Removing = 'removing';
}
