<?php

declare(strict_types=1);

namespace App\Application\Queries\Evaluation;

use App\Application\CQRS\Contracts\QueryHandlerInterface;
use App\Application\CQRS\Contracts\QueryInterface;
use App\Domain\Repositories\EvaluationRepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;

final class GetEvaluationsHandler implements QueryHandlerInterface
{
    public function __construct(private readonly EvaluationRepositoryInterface $repository) {}

    public function handle(QueryInterface $query): LengthAwarePaginator
    {
        /** @var GetEvaluationsQuery $query */
        return $this->repository->paginate($query->page, $query->perPage, $query->projectId);
    }
}
