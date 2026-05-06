<?php

declare(strict_types=1);

namespace App\Application\CQRS\Contracts;

interface CommandHandlerInterface
{
    public function handle(CommandInterface $command): mixed;
}
