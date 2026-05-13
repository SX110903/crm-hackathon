<?php

declare(strict_types=1);

namespace App\Application\Commands\Team;

use App\Application\CQRS\Contracts\CommandInterface;

final class DeleteTeamCommand implements CommandInterface
{
    public function __construct(public readonly int $id) {}
}
