<?php

namespace SebastianSulinski\LaravelForgeSdk\Enums\Certificate;

enum KeyType: string
{
    case Ecdsa = 'ecdsa';
    case Rsa = 'rsa';
}
