<?php

declare(strict_types=1);

namespace App\Shared\Application\Bus;

interface CommandBusInterface
{
    public function handle(CommandInterface $command): mixed;
}
