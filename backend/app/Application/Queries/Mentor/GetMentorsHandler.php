<?php

declare(strict_types=1);

namespace App\Application\Queries\Mentor;

use App\Application\CQRS\Contracts\QueryHandlerInterface;
use App\Application\CQRS\Contracts\QueryInterface;
use App\Domain\Repositories\MentorRepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;

final class GetMentorsHandler implements QueryHandlerInterface
{
    public function __construct(private readonly MentorRepositoryInterface $repository) {}

    public function handle(QueryInterface $query): LengthAwarePaginator
    {
        /** @var GetMentorsQuery $query */
        return $this->repository->paginate($query->page, $query->perPage);
    }
}
