<?php

declare(strict_types=1);

namespace App\Application\Commands\Judge;

use App\Application\CQRS\Contracts\CommandHandlerInterface;
use App\Application\CQRS\Contracts\CommandInterface;
use App\Domain\Repositories\JudgeRepositoryInterface;

final class DeleteJudgeHandler implements CommandHandlerInterface
{
    public function __construct(private readonly JudgeRepositoryInterface $repository) {}

    public function handle(CommandInterface $command): bool
    {
        /** @var DeleteJudgeCommand $command */
        return $this->repository->delete($command->id);
    }
}
