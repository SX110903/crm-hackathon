<?php

declare(strict_types=1);

namespace App\Domain\Repositories;

use App\Models\Project;
use Illuminate\Pagination\LengthAwarePaginator;

interface ProjectRepositoryInterface
{
    public function create(array $data): Project;

    public function update(int $id, array $data): Project;

    public function delete(int $id): bool;

    public function findById(int $id): Project;

    public function paginate(int $page, int $perPage, ?string $search, ?string $status): LengthAwarePaginator;
}
