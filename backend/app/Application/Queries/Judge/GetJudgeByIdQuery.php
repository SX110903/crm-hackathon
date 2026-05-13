<?php

declare(strict_types=1);

namespace App\Application\Queries\Judge;

use App\Application\CQRS\Contracts\QueryInterface;

final class GetJudgeByIdQuery implements QueryInterface
{
    public function __construct(public readonly int $id) {}
}
