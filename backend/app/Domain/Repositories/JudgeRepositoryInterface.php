<?php

declare(strict_types=1);

namespace App\Domain\Repositories;

use App\Models\Judge;
use Illuminate\Pagination\LengthAwarePaginator;

interface JudgeRepositoryInterface
{
    public function create(array $data): Judge;

    public function update(int $id, array $data): Judge;

    public function delete(int $id): bool;

    public function findById(int $id): Judge;

    public function paginate(int $page, int $perPage): LengthAwarePaginator;
}
