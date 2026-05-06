<?php

declare(strict_types=1);

namespace App\Application\Commands\Team;

use App\Application\CQRS\Contracts\CommandHandlerInterface;
use App\Application\CQRS\Contracts\CommandInterface;
use App\Domain\Repositories\TeamRepositoryInterface;

final class AddTeamMemberHandler implements CommandHandlerInterface
{
    public function __construct(private readonly TeamRepositoryInterface $repository) {}

    public function handle(CommandInterface $command): mixed
    {
        /** @var AddTeamMemberCommand $command */
        return $this->repository->addMember($command->teamId, $command->participantId, $command->role);
    }
}
