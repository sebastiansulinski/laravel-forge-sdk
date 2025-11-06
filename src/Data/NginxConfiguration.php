<?php

namespace SebastianSulinski\LaravelForgeSdk\Data;

readonly class NginxConfiguration
{
    /**
     * NginxConfiguration constructor.
     */
    public function __construct(
        public string $id,
        public string $content,
    ) {}
}
