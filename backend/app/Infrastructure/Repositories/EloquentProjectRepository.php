<?php

declare(strict_types=1);

namespace App\Infrastructure\Repositories;

use App\Domain\Repositories\ProjectRepositoryInterface;
use App\Models\Project;
use Illuminate\Pagination\LengthAwarePaginator;

final class EloquentProjectRepository implements ProjectRepositoryInterface
{
    public function create(array $data): Project
    {
        return Project::create($data);
    }

    public function update(int $id, array $data): Project
    {
        $project = $this->findById($id);
        $project->update($data);

        return $project->fresh();
    }

    public function delete(int $id): bool
    {
        return (bool) $this->findById($id)->delete();
    }

    public function findById(int $id): Project
    {
        return Project::findOrFail($id);
    }

    public function paginate(int $page, int $perPage, ?string $search, ?string $status): LengthAwarePaginator
    {
        return Project::when(
            $search,
            fn ($q) => $q->where('name', 'like', "%{$search}%")
                         ->orWhere('description', 'like', "%{$search}%")
                         ->orWhere('category', 'like', "%{$search}%")
        )->when(
            $status,
            fn ($q) => $q->where('status', $status)
        )->paginate($perPage, ['*'], 'page', $page);
    }
}
