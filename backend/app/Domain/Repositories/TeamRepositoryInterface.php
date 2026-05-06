<?php

declare(strict_types=1);

namespace App\Domain\Repositories;

use App\Models\Team;
use Illuminate\Pagination\LengthAwarePaginator;

interface TeamRepositoryInterface
{
    public function create(array $data): Team;

    public function update(int $id, array $data): Team;

    public function delete(int $id): bool;

    public function findById(int $id): Team;

    public function paginate(int $page, int $perPage, ?string $search): LengthAwarePaginator;

    public function addMember(int $teamId, int $participantId, string $role): mixed;

    public function removeMember(int $teamId, int $participantId): bool;
}
