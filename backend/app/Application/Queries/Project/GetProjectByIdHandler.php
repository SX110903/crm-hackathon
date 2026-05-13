<?php

declare(strict_types=1);

namespace App\Application\Queries\Project;

use App\Application\CQRS\Contracts\QueryHandlerInterface;
use App\Application\CQRS\Contracts\QueryInterface;
use App\Domain\Repositories\ProjectRepositoryInterface;
use App\Models\Project;

final class GetProjectByIdHandler implements QueryHandlerInterface
{
    public function __construct(private readonly ProjectRepositoryInterface $repository) {}

    public function handle(QueryInterface $query): Project
    {
        /** @var GetProjectByIdQuery $query */
        return $this->repository->findById($query->id);
    }
}
