<?php

namespace SebastianSulinski\LaravelForgeSdk\Enums;

enum CertificateVerificationMethod: string
{
    case Http01 = 'http-01';
    case Dns01 = 'dns-01';
}
