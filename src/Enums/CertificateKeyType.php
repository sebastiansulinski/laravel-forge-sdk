<?php

namespace SebastianSulinski\LaravelForgeSdk\Enums;

enum CertificateKeyType: string
{
    case Ecdsa = 'ecdsa';
    case Rsa = 'rsa';
}
