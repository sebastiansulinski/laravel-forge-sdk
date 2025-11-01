<?php

namespace SebastianSulinski\LaravelForgeSdk\Enums\Certificate;

enum RequestStatus: string
{
    case Verifying = 'verifying';
    case Creating = 'creating';
    case Created = 'created';
}
