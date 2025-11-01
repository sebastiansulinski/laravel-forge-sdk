<?php

namespace SebastianSulinski\LaravelForgeSdk\Enums\Site;

enum WwwRedirectType: string
{
    case None = 'none';
    case FromWww = 'from-www';
    case ToWww = 'to-www';
}
