<?php

namespace SebastianSulinski\LaravelForgeSdk\Enums;

enum MaintenanceModeStatus: string
{
    case Disabling = 'disabling';
    case Enabling = 'enabling';
}
