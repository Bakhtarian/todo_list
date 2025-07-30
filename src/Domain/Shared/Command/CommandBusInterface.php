<?php

namespace App\Domain\Shared\Command;

interface CommandBusInterface
{
    public function dispatch(CommandInterface $command): void;
}
