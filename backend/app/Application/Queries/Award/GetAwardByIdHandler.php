<?php

declare(strict_types=1);

namespace App\Application\Queries\Award;

use App\Application\CQRS\Contracts\QueryHandlerInterface;
use App\Application\CQRS\Contracts\QueryInterface;
use App\Domain\Repositories\AwardRepositoryInterface;
use App\Models\Award;

final class GetAwardByIdHandler implements QueryHandlerInterface
{
    public function __construct(private readonly AwardRepositoryInterface $repository) {}

    public function handle(QueryInterface $query): Award
    {
        /** @var GetAwardByIdQuery $query */
        return $this->repository->findById($query->id);
    }
}
