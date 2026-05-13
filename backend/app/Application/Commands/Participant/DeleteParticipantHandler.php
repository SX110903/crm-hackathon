<?php

declare(strict_types=1);

namespace App\Application\Commands\Participant;

use App\Application\CQRS\Contracts\CommandHandlerInterface;
use App\Application\CQRS\Contracts\CommandInterface;
use App\Domain\Repositories\ParticipantRepositoryInterface;

final class DeleteParticipantHandler implements CommandHandlerInterface
{
    public function __construct(private readonly ParticipantRepositoryInterface $repository) {}

    public function handle(CommandInterface $command): bool
    {
        /** @var DeleteParticipantCommand $command */
        return $this->repository->delete($command->id);
    }
}
