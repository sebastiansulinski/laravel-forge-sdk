<?php

namespace SebastianSulinski\LaravelForgeSdk\Enums;

enum CertificateType: string
{
    case LetsEncrypt = 'letsencrypt';
    case Csr = 'csr';
    case Existing = 'existing';
}
