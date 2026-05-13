<?php

declare(strict_types=1);

namespace App\Infrastructure\Repositories;

use App\Domain\Repositories\MentorRepositoryInterface;
use App\Models\Mentor;
use Illuminate\Pagination\LengthAwarePaginator;

final class EloquentMentorRepository implements MentorRepositoryInterface
{
    public function create(array $data): Mentor
    {
        return Mentor::create($data);
    }

    public function update(int $id, array $data): Mentor
    {
        $mentor = $this->findById($id);
        $mentor->update($data);

        return $mentor->fresh();
    }

    public function delete(int $id): bool
    {
        return (bool) $this->findById($id)->delete();
    }

    public function findById(int $id): Mentor
    {
        return Mentor::findOrFail($id);
    }

    public function paginate(int $page, int $perPage): LengthAwarePaginator
    {
        return Mentor::paginate($perPage, ['*'], 'page', $page);
    }
}
