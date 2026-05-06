<?php

declare(strict_types=1);

namespace App\Application\Commands\Award;

use App\Application\CQRS\Contracts\CommandHandlerInterface;
use App\Application\CQRS\Contracts\CommandInterface;
use App\Domain\Repositories\AwardRepositoryInterface;

final class DeleteAwardHandler implements CommandHandlerInterface
{
    public function __construct(private readonly AwardRepositoryInterface $repository) {}

    public function handle(CommandInterface $command): bool
    {
        /** @var DeleteAwardCommand $command */
        return $this->repository->delete($command->id);
    }
}
