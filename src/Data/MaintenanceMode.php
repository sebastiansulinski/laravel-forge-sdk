<?php

namespace SebastianSulinski\LaravelForgeSdk\Data;

use SebastianSulinski\LaravelForgeSdk\Enums\MaintenanceModeStatus;

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
