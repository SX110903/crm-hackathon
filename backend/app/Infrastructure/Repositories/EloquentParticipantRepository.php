<?php

declare(strict_types=1);

namespace App\Infrastructure\Repositories;

use App\Domain\Repositories\ParticipantRepositoryInterface;
use App\Models\Participant;
use Illuminate\Pagination\LengthAwarePaginator;

final class EloquentParticipantRepository implements ParticipantRepositoryInterface
{
    public function create(array $data): Participant
    {
        return Participant::create($data);
    }

    public function update(int $id, array $data): Participant
    {
        $participant = $this->findById($id);
        $participant->update($data);

        return $participant->fresh();
    }

    public function delete(int $id): bool
    {
        return (bool) $this->findById($id)->delete();
    }

    public function findById(int $id): Participant
    {
        return Participant::findOrFail($id);
    }

    public function paginate(int $page, int $perPage, ?string $search): LengthAwarePaginator
    {
        return Participant::when(
            $search,
            fn ($q) => $q->where('first_name', 'like', "%{$search}%")
                         ->orWhere('last_name', 'like', "%{$search}%")
                         ->orWhere('email', 'like', "%{$search}%")
        )->paginate($perPage, ['*'], 'page', $page);
    }
}
