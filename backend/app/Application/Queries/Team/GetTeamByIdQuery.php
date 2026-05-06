<?php

declare(strict_types=1);

namespace App\Application\Queries\Team;

use App\Application\CQRS\Contracts\QueryInterface;

final class GetTeamByIdQuery implements QueryInterface
{
    public function __construct(public readonly int $id) {}
}
