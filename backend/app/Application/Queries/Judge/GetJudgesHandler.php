<?php

declare(strict_types=1);

namespace App\Application\Queries\Judge;

use App\Application\CQRS\Contracts\QueryHandlerInterface;
use App\Application\CQRS\Contracts\QueryInterface;
use App\Domain\Repositories\JudgeRepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;

final class GetJudgesHandler implements QueryHandlerInterface
{
    public function __construct(private readonly JudgeRepositoryInterface $repository) {}

    public function handle(QueryInterface $query): LengthAwarePaginator
    {
        /** @var GetJudgesQuery $query */
        return $this->repository->paginate($query->page, $query->perPage);
    }
}
