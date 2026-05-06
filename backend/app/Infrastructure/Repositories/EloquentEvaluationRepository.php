<?php

declare(strict_types=1);

namespace App\Infrastructure\Repositories;

use App\Domain\Repositories\EvaluationRepositoryInterface;
use App\Models\Evaluation;
use Illuminate\Pagination\LengthAwarePaginator;

final class EloquentEvaluationRepository implements EvaluationRepositoryInterface
{
    public function create(array $data): Evaluation
    {
        return Evaluation::create($data);
    }

    public function findById(int $id): Evaluation
    {
        return Evaluation::findOrFail($id);
    }

    public function paginate(int $page, int $perPage, ?int $projectId): LengthAwarePaginator
    {
        return Evaluation::when(
            $projectId,
            fn ($q) => $q->where('project_id', $projectId)
        )->paginate($perPage, ['*'], 'page', $page);
    }
}
