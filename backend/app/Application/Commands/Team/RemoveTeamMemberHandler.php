<?php

declare(strict_types=1);

namespace App\Application\Commands\Team;

use App\Application\CQRS\Contracts\CommandHandlerInterface;
use App\Application\CQRS\Contracts\CommandInterface;
use App\Domain\Repositories\TeamRepositoryInterface;

final class RemoveTeamMemberHandler implements CommandHandlerInterface
{
    public function __construct(private readonly TeamRepositoryInterface $repository) {}

    public function handle(CommandInterface $command): bool
    {
        /** @var RemoveTeamMemberCommand $command */
        return $this->repository->removeMember($command->teamId, $command->participantId);
    }
}

