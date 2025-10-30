<?php

namespace SebastianSulinski\LaravelForgeSdk\Enums;

enum CertificateRequestStatus: string
{
    case Verifying = 'verifying';
    case Creating = 'creating';
    case Created = 'created';
}
