<?php

declare(strict_types=1);

namespace App\Infrastructure\Repositories;

use App\Domain\Repositories\JudgeRepositoryInterface;
use App\Models\Judge;
use Illuminate\Pagination\LengthAwarePaginator;

final class EloquentJudgeRepository implements JudgeRepositoryInterface
{
    public function create(array $data): Judge
    {
        return Judge::create($data);
    }

    public function update(int $id, array $data): Judge
    {
        $judge = $this->findById($id);
        $judge->update($data);

        return $judge->fresh();
    }

    public function delete(int $id): bool
    {
        return (bool) $this->findById($id)->delete();
    }

    public function findById(int $id): Judge
    {
        return Judge::findOrFail($id);
    }

    public function paginate(int $page, int $perPage): LengthAwarePaginator
    {
        return Judge::paginate($perPage, ['*'], 'page', $page);
    }
}
