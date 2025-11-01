<?php

namespace SebastianSulinski\LaravelForgeSdk\Enums\Site;

enum MaintenanceModeStatus: string
{
    case Enabling = 'enabling';
    case Disabling = 'disabling';
}
