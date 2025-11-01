<?php

namespace SebastianSulinski\LaravelForgeSdk\Enums\Repository;

enum Provider: string
{
    case Github = 'github';
    case Gitlab = 'gitlab';
    case Bitbucket = 'bitbucket';
    case GitlabCustom = 'gitlab-custom';
    case Custom = 'custom';
}
