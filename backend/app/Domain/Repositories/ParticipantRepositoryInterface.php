<?php

declare(strict_types=1);

namespace App\Domain\Repositories;

use App\Models\Participant;
use Illuminate\Pagination\LengthAwarePaginator;

interface ParticipantRepositoryInterface
{
    public function create(array $data): Participant;

    public function update(int $id, array $data): Participant;

    public function delete(int $id): bool;

    public function findById(int $id): Participant;

    public function paginate(int $page, int $perPage, ?string $search): LengthAwarePaginator;
}
