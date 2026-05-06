<?php

declare(strict_types=1);

namespace App\Application\Queries\Participant;

use App\Application\CQRS\Contracts\QueryHandlerInterface;
use App\Application\CQRS\Contracts\QueryInterface;
use App\Domain\Repositories\ParticipantRepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;

final class GetParticipantsHandler implements QueryHandlerInterface
{
    public function __construct(private readonly ParticipantRepositoryInterface $repository) {}

    public function handle(QueryInterface $query): LengthAwarePaginator
    {
        /** @var GetParticipantsQuery $query */
        return $this->repository->paginate($query->page, $query->perPage, $query->search);
    }
}
