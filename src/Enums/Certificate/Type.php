<?php

namespace SebastianSulinski\LaravelForgeSdk\Enums\Certificate;

enum Type: string
{
    case LetsEncrypt = 'letsencrypt';
    case Csr = 'csr';
    case Existing = 'existing';
}
