<?php

declare(strict_types=1);

namespace App\Application\Queries\Project;

use App\Application\CQRS\Contracts\QueryInterface;

final class GetProjectByIdQuery implements QueryInterface
{
    public function __construct(public readonly int $id) {}
}
