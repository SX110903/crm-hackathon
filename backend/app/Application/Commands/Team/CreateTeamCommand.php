<?php

declare(strict_types=1);

namespace App\Application\Commands\Team;

use App\Application\CQRS\Contracts\CommandInterface;

final class CreateTeamCommand implements CommandInterface
{
    public function __construct(
        public readonly string $name,
        public readonly int $maxMembers,
        public readonly ?int $leaderId,
    ) {}
}
