<?php

declare(strict_types=1);

namespace App\Application\Commands\Team;

use App\Application\CQRS\Contracts\CommandHandlerInterface;
use App\Application\CQRS\Contracts\CommandInterface;
use App\Domain\Repositories\TeamRepositoryInterface;
use App\Models\Team;

final class CreateTeamHandler implements CommandHandlerInterface
{
    public function __construct(private readonly TeamRepositoryInterface $repository) {}

    public function handle(CommandInterface $command): Team
    {
        /** @var CreateTeamCommand $command */
        return $this->repository->create([
            'name'        => $command->name,
            'max_members' => $command->maxMembers,
            'leader_id'   => $command->leaderId,
        ]);
    }
}
