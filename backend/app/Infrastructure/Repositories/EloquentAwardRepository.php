<?php

declare(strict_types=1);

namespace App\Infrastructure\Repositories;

use App\Domain\Repositories\AwardRepositoryInterface;
use App\Models\Award;
use Illuminate\Pagination\LengthAwarePaginator;

final class EloquentAwardRepository implements AwardRepositoryInterface
{
    public function create(array $data): Award
    {
        return Award::create($data);
    }

    public function update(int $id, array $data): Award
    {
        $award = $this->findById($id);
        $award->update($data);

        return $award->fresh();
    }

    public function delete(int $id): bool
    {
        return (bool) $this->findById($id)->delete();
    }

    public function findById(int $id): Award
    {
        return Award::findOrFail($id);
    }

    public function paginate(int $page, int $perPage): LengthAwarePaginator
    {
        return Award::paginate($perPage, ['*'], 'page', $page);
    }

    public function assignToProject(int $awardId, int $projectId): Award
    {
        $award = $this->findById($awardId);
        $award->update(['project_id' => $projectId]);

        return $award->fresh();
    }
}
