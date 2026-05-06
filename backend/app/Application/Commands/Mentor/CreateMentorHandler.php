<?php

declare(strict_types=1);

namespace App\Application\Commands\Mentor;

use App\Application\CQRS\Contracts\CommandHandlerInterface;
use App\Application\CQRS\Contracts\CommandInterface;
use App\Domain\Repositories\MentorRepositoryInterface;
use App\Models\Mentor;

final class CreateMentorHandler implements CommandHandlerInterface
{
    public function __construct(private readonly MentorRepositoryInterface $repository) {}

    public function handle(CommandInterface $command): Mentor
    {
        /** @var CreateMentorCommand $command */
        return $this->repository->create([
            'first_name'       => $command->firstName,
            'last_name'        => $command->lastName,
            'email'            => $command->email,
            'company'          => $command->company,
            'specialization'   => $command->specialization,
            'available_slots'  => $command->availableSlots,
        ]);
    }
}
