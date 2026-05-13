<?php

declare(strict_types=1);

namespace App\Application\Commands\Participant;

use App\Application\CQRS\Contracts\CommandHandlerInterface;
use App\Application\CQRS\Contracts\CommandInterface;
use App\Domain\Repositories\ParticipantRepositoryInterface;
use App\Models\Participant;

final class ChangeParticipantPasswordHandler implements CommandHandlerInterface
{
    public function __construct(private readonly ParticipantRepositoryInterface $repository) {}

    public function handle(CommandInterface $command): Participant
    {
        /** @var ChangeParticipantPasswordCommand $command */
        return $this->repository->update($command->id, [
            'password' => $command->newPassword,
        ]);
    }
}
