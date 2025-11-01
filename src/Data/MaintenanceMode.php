<?php

namespace SebastianSulinski\LaravelForgeSdk\Data;

use SebastianSulinski\LaravelForgeSdk\Enums\Site\MaintenanceModeStatus;

readonly class MaintenanceMode
{
    /**
     * MaintenanceMode constructor.
     */
    public function __construct(
        public ?bool $enabled,
        public ?MaintenanceModeStatus $status = null,
    ) {}
}
