<?php

declare(strict_types=1);

namespace App\Application\Queries\Mentor;

use App\Application\CQRS\Contracts\QueryHandlerInterface;
use App\Application\CQRS\Contracts\QueryInterface;
use App\Domain\Repositories\MentorRepositoryInterface;
use App\Models\Mentor;

final class GetMentorByIdHandler implements QueryHandlerInterface
{
    public function __construct(private readonly MentorRepositoryInterface $repository) {}

    public function handle(QueryInterface $query): Mentor
    {
        /** @var GetMentorByIdQuery $query */
        return $this->repository->findById($query->id);
    }
}
