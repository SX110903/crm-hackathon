<?php

declare(strict_types=1);

namespace App\Application\Queries\Evaluation;

use App\Application\CQRS\Contracts\QueryInterface;

final class GetEvaluationByIdQuery implements QueryInterface
{
    public function __construct(public readonly int $id) {}
}
