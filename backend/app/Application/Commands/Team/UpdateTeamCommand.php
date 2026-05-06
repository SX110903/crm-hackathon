<?php

declare(strict_types=1);

namespace App\Application\Commands\Team;

use App\Application\CQRS\Contracts\CommandInterface;

final class UpdateTeamCommand implements CommandInterface
{
    public function __construct(
        public readonly int $id,
        public readonly string $name,
        public readonly int $maxMembers,
        public readonly ?int $leaderId,
    ) {}
}
