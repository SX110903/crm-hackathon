<?php

declare(strict_types=1);

namespace App\Infrastructure\Repositories;

use App\Domain\Repositories\TeamRepositoryInterface;
use App\Models\Team;
use App\Models\TeamMember;
use Illuminate\Pagination\LengthAwarePaginator;

final class EloquentTeamRepository implements TeamRepositoryInterface
{
    public function create(array $data): Team
    {
        return Team::create($data);
    }

    public function update(int $id, array $data): Team
    {
        $team = $this->findById($id);
        $team->update($data);

        return $team->fresh();
    }

    public function delete(int $id): bool
    {
        return (bool) $this->findById($id)->delete();
    }

    public function findById(int $id): Team
    {
        return Team::findOrFail($id);
    }

    public function paginate(int $page, int $perPage, ?string $search): LengthAwarePaginator
    {
        return Team::when(
            $search,
            fn ($q) => $q->where('name', 'like', "%{$search}%")
        )->paginate($perPage, ['*'], 'page', $page);
    }

    public function addMember(int $teamId, int $participantId, string $role): TeamMember
    {
        return TeamMember::create([
            'team_id'        => $teamId,
            'participant_id' => $participantId,
            'role'           => $role,
        ]);
    }

    public function removeMember(int $teamId, int $participantId): bool
    {
        return (bool) TeamMember::where('team_id', $teamId)
            ->where('participant_id', $participantId)
            ->delete();
    }
}
