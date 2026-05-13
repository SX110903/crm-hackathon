<?php

declare(strict_types=1);

namespace App\Application\Queries\Project;

use App\Application\CQRS\Contracts\QueryHandlerInterface;
use App\Application\CQRS\Contracts\QueryInterface;
use App\Domain\Repositories\ProjectRepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;

final class GetProjectsHandler implements QueryHandlerInterface
{
    public function __construct(private readonly ProjectRepositoryInterface $repository) {}

    public function handle(QueryInterface $query): LengthAwarePaginator
    {
        /** @var GetProjectsQuery $query */
        return $this->repository->paginate($query->page, $query->perPage, $query->search, $query->status);
    }
}
