<?php

namespace SebastianSulinski\LaravelForgeSdk\Enums;

enum WwwRedirectType: string
{
    case None = 'none';
    case FromWww = 'from-www';
    case ToWww = 'to-www';
}
