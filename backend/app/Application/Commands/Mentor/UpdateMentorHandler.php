<?php

declare(strict_types=1);

namespace App\Application\Commands\Mentor;

use App\Application\CQRS\Contracts\CommandHandlerInterface;
use App\Application\CQRS\Contracts\CommandInterface;
use App\Domain\Repositories\MentorRepositoryInterface;
use App\Models\Mentor;

final class UpdateMentorHandler implements CommandHandlerInterface
{
    public function __construct(private readonly MentorRepositoryInterface $repository) {}

    public function handle(CommandInterface $command): Mentor
    {
        /** @var UpdateMentorCommand $command */
        return $this->repository->update($command->id, [
            'first_name'      => $command->firstName,
            'last_name'       => $command->lastName,
            'email'           => $command->email,
            'company'         => $command->company,
            'specialization'  => $command->specialization,
            'available_slots' => $command->availableSlots,
        ]);
    }
}
