<?php

declare(strict_types=1);

namespace App\Application\Commands\Participant;

use App\Application\CQRS\Contracts\CommandHandlerInterface;
use App\Application\CQRS\Contracts\CommandInterface;
use App\Domain\Repositories\ParticipantRepositoryInterface;
use App\Models\Participant;

final class RegisterParticipantHandler implements CommandHandlerInterface
{
    public function __construct(private readonly ParticipantRepositoryInterface $repository) {}

    public function handle(CommandInterface $command): Participant
    {
        /** @var RegisterParticipantCommand $command */
        return $this->repository->create([
            'first_name'    => $command->firstName,
            'last_name'     => $command->lastName,
            'email'         => $command->email,
            'password'      => $command->password,
            'phone'         => $command->phone,
            'university'    => $command->university,
            'major'         => $command->major,
            'year_of_study' => $command->yearOfStudy,
        ]);
    }
}
