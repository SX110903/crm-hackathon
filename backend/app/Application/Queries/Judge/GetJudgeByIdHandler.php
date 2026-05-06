<?php

declare(strict_types=1);

namespace App\Application\Queries\Judge;

use App\Application\CQRS\Contracts\QueryHandlerInterface;
use App\Application\CQRS\Contracts\QueryInterface;
use App\Domain\Repositories\JudgeRepositoryInterface;
use App\Models\Judge;

final class GetJudgeByIdHandler implements QueryHandlerInterface
{
    public function __construct(private readonly JudgeRepositoryInterface $repository) {}

    public function handle(QueryInterface $query): Judge
    {
        /** @var GetJudgeByIdQuery $query */
        return $this->repository->findById($query->id);
    }
}
