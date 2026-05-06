<?php

declare(strict_types=1);

namespace App\Application\Commands\Participant;

use App\Application\CQRS\Contracts\CommandHandlerInterface;
use App\Application\CQRS\Contracts\CommandInterface;
use App\Domain\Repositories\ParticipantRepositoryInterface;
use App\Models\Participant;

final class UpdateParticipantHandler implements CommandHandlerInterface
{
    public function __construct(private readonly ParticipantRepositoryInterface $repository) {}

    public function handle(CommandInterface $command): Participant
    {
        /** @var UpdateParticipantCommand $command */
        return $this->repository->update($command->id, [
            'first_name'    => $command->firstName,
            'last_name'     => $command->lastName,
            'email'         => $command->email,
            'phone'         => $command->phone,
            'university'    => $command->university,
            'major'         => $command->major,
            'year_of_study' => $command->yearOfStudy,
        ]);
    }
}
