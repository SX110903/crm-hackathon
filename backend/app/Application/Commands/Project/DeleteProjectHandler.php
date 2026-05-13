<?php

declare(strict_types=1);

namespace App\Application\Commands\Project;

use App\Application\CQRS\Contracts\CommandHandlerInterface;
use App\Application\CQRS\Contracts\CommandInterface;
use App\Domain\Repositories\ProjectRepositoryInterface;

final class DeleteProjectHandler implements CommandHandlerInterface
{
    public function __construct(private readonly ProjectRepositoryInterface $repository) {}

    public function handle(CommandInterface $command): bool
    {
        /** @var DeleteProjectCommand $command */
        return $this->repository->delete($command->id);
    }
}
