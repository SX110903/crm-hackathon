<?php

declare(strict_types=1);

namespace App\Application\Commands\Award;

use App\Application\CQRS\Contracts\CommandHandlerInterface;
use App\Application\CQRS\Contracts\CommandInterface;
use App\Domain\Repositories\AwardRepositoryInterface;
use App\Models\Award;

final class CreateAwardHandler implements CommandHandlerInterface
{
    public function __construct(private readonly AwardRepositoryInterface $repository) {}

    public function handle(CommandInterface $command): Award
    {
        /** @var CreateAwardCommand $command */
        return $this->repository->create([
            'name'       => $command->name,
            'category'   => $command->category,
            'prize'      => $command->prize,
            'project_id' => $command->projectId,
        ]);
    }
}
