<?php

declare(strict_types=1);

namespace App\Application\Commands\Mentor;

use App\Application\CQRS\Contracts\CommandHandlerInterface;
use App\Application\CQRS\Contracts\CommandInterface;
use App\Domain\Repositories\MentorRepositoryInterface;

final class DeleteMentorHandler implements CommandHandlerInterface
{
    public function __construct(private readonly MentorRepositoryInterface $repository) {}

    public function handle(CommandInterface $command): bool
    {
        /** @var DeleteMentorCommand $command */
        return $this->repository->delete($command->id);
    }
}
