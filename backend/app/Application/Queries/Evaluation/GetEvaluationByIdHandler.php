<?php

declare(strict_types=1);

namespace App\Application\Queries\Evaluation;

use App\Application\CQRS\Contracts\QueryHandlerInterface;
use App\Application\CQRS\Contracts\QueryInterface;
use App\Domain\Repositories\EvaluationRepositoryInterface;
use App\Models\Evaluation;

final class GetEvaluationByIdHandler implements QueryHandlerInterface
{
    public function __construct(private readonly EvaluationRepositoryInterface $repository) {}

    public function handle(QueryInterface $query): Evaluation
    {
        /** @var GetEvaluationByIdQuery $query */
        return $this->repository->findById($query->id);
    }
}
