<?php

declare(strict_types=1);

namespace App\Application\Queries\Participant;

use App\Application\CQRS\Contracts\QueryHandlerInterface;
use App\Application\CQRS\Contracts\QueryInterface;
use App\Domain\Repositories\ParticipantRepositoryInterface;
use App\Models\Participant;

final class GetParticipantByIdHandler implements QueryHandlerInterface
{
    public function __construct(private readonly ParticipantRepositoryInterface $repository) {}

    public function handle(QueryInterface $query): Participant
    {
        /** @var GetParticipantByIdQuery $query */
        return $this->repository->findById($query->id);
    }
}
