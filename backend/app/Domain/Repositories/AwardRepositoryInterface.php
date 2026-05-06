<?php

declare(strict_types=1);

namespace App\Domain\Repositories;

use App\Models\Award;
use Illuminate\Pagination\LengthAwarePaginator;

interface AwardRepositoryInterface
{
    public function create(array $data): Award;

    public function update(int $id, array $data): Award;

    public function delete(int $id): bool;

    public function findById(int $id): Award;

    public function paginate(int $page, int $perPage): LengthAwarePaginator;

    public function assignToProject(int $awardId, int $projectId): Award;
}
