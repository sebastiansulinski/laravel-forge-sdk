<?php

namespace SebastianSulinski\LaravelForgeSdk\Enums;

enum SiteType: string
{
    case Laravel = 'laravel';
    case Symfony = 'symfony';
    case Statamic = 'statamic';
    case Wordpress = 'wordpress';
    case PhpMyAdmin = 'phpmyadmin';
    case Php = 'php';
    case NextJs = 'next.js';
    case NuxtJs = 'nuxt.js';
    case StaticHtml = 'static-html';
    case Other = 'other';
    case Custom = 'custom';
}
