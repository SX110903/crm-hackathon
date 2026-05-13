<?php

declare(strict_types=1);

namespace App\Application\Queries\Team;

use App\Application\CQRS\Contracts\QueryHandlerInterface;
use App\Application\CQRS\Contracts\QueryInterface;
use App\Domain\Repositories\TeamRepositoryInterface;
use App\Models\Team;

final class GetTeamByIdHandler implements QueryHandlerInterface
{
    public function __construct(private readonly TeamRepositoryInterface $repository) {}

    public function handle(QueryInterface $query): Team
    {
        /** @var GetTeamByIdQuery $query */
        return $this->repository->findById($query->id);
    }
}
