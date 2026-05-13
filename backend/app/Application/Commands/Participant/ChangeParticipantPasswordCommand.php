<?php

declare(strict_types=1);

namespace App\Application\Commands\Participant;

use App\Application\CQRS\Contracts\CommandInterface;

final class ChangeParticipantPasswordCommand implements CommandInterface
{
    public function __construct(
        public readonly int $id,
        public readonly string $newPassword,
    ) {}
}
