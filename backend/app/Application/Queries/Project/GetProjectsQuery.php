<?php

declare(strict_types=1);

namespace App\Application\Queries\Project;

use App\Application\CQRS\Contracts\QueryInterface;

final class GetProjectsQuery implements QueryInterface
{
    public function __construct(
        public readonly int $page = 1,
        public readonly int $perPage = 15,
        public readonly ?string $search = null,
        public readonly ?string $status = null,
    ) {}
}
