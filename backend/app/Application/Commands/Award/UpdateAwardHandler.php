<?php

declare(strict_types=1);

namespace App\Application\Commands\Award;

use App\Application\CQRS\Contracts\CommandHandlerInterface;
use App\Application\CQRS\Contracts\CommandInterface;
use App\Domain\Repositories\AwardRepositoryInterface;
use App\Models\Award;

final class UpdateAwardHandler implements CommandHandlerInterface
{
    public function __construct(private readonly AwardRepositoryInterface $repository) {}

    public function handle(CommandInterface $command): Award
    {
        /** @var UpdateAwardCommand $command */
        return $this->repository->update($command->id, [
            'name'     => $command->name,
            'category' => $command->category,
            'prize'    => $command->prize,
        ]);
    }
}
