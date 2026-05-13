<?php

declare(strict_types=1);

namespace App\Domain\Repositories;

use App\Models\Evaluation;
use Illuminate\Pagination\LengthAwarePaginator;

interface EvaluationRepositoryInterface
{
    public function create(array $data): Evaluation;

    public function findById(int $id): Evaluation;

    public function paginate(int $page, int $perPage, ?int $projectId): LengthAwarePaginator;
}
