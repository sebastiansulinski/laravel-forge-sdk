<?php

namespace SebastianSulinski\LaravelForgeSdk\Enums\Command;

enum Status: string
{
    case Waiting = 'waiting';
    case Running = 'running';
    case Finished = 'finished';
    case Timeout = 'timeout';
    case Failed = 'failed';
}
