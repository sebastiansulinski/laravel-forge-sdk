<?php

namespace SebastianSulinski\LaravelForgeSdk\Enums;

enum DomainStatus: string
{
    case Pending = 'pending';
    case Connecting = 'connecting';
    case Enabled = 'enabled';
    case Removing = 'removing';
    case Securing = 'securing';
    case Updating = 'updating';
    case Disabling = 'disabling';
    case Disabled = 'disabled';
    case Enabling = 'enabling';
}
