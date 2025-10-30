<?php

namespace SebastianSulinski\LaravelForgeSdk\Enums;

enum DomainType: string
{
    case Primary = 'primary';
    case Alias = 'alias';
}
