<?php

namespace SebastianSulinski\LaravelForgeSdk\Enums\Site;

enum DomainMode: string
{
    case OnForge = 'on-forge';
    case Custom = 'custom';
}
