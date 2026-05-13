<?php

declare(strict_types=1);

namespace App\Application\Queries\Evaluation;

use App\Application\CQRS\Contracts\QueryInterface;

final class GetEvaluationsQuery implements QueryInterface
{
    public function __construct(
        public readonly int $page = 1,
        public readonly int $perPage = 15,
        public readonly ?int $projectId = null,
    ) {}
}
