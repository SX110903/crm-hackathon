<?php

declare(strict_types=1);

namespace App\Application\Queries\Award;

use App\Application\CQRS\Contracts\QueryHandlerInterface;
use App\Application\CQRS\Contracts\QueryInterface;
use App\Domain\Repositories\AwardRepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;

final class GetAwardsHandler implements QueryHandlerInterface
{
    public function __construct(private readonly AwardRepositoryInterface $repository) {}

    public function handle(QueryInterface $query): LengthAwarePaginator
    {
        /** @var GetAwardsQuery $query */
        return $this->repository->paginate($query->page, $query->perPage);
    }
}
