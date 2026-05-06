<?php

declare(strict_types=1);

namespace App\Application\Queries\Team;

use App\Application\CQRS\Contracts\QueryHandlerInterface;
use App\Application\CQRS\Contracts\QueryInterface;
use App\Domain\Repositories\TeamRepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;

final class GetTeamsHandler implements QueryHandlerInterface
{
    public function __construct(private readonly TeamRepositoryInterface $repository) {}

    public function handle(QueryInterface $query): LengthAwarePaginator
    {
        /** @var GetTeamsQuery $query */
        return $this->repository->paginate($query->page, $query->perPage, $query->search);
    }
}
