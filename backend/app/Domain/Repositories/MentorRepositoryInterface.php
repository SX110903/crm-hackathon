<?php

declare(strict_types=1);

namespace App\Domain\Repositories;

use App\Models\Mentor;
use Illuminate\Pagination\LengthAwarePaginator;

interface MentorRepositoryInterface
{
    public function create(array $data): Mentor;

    public function update(int $id, array $data): Mentor;

    public function delete(int $id): bool;

    public function findById(int $id): Mentor;

    public function paginate(int $page, int $perPage): LengthAwarePaginator;
}
