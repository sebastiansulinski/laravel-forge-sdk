<?php

namespace SebastianSulinski\LaravelForgeSdk\Enums\Certificate;

enum VerificationMethod: string
{
    case Http01 = 'http-01';
    case Dns01 = 'dns-01';
}
