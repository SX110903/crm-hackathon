<?php

declare(strict_types=1);

namespace App\Application\Commands\Participant;

use App\Application\CQRS\Contracts\CommandInterface;

final class DeleteParticipantCommand implements CommandInterface
{
    public function __construct(public readonly int $id) {}
}
