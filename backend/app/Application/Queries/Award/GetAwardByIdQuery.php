<?php

declare(strict_types=1);

namespace App\Application\Queries\Award;

use App\Application\CQRS\Contracts\QueryInterface;

final class GetAwardByIdQuery implements QueryInterface
{
    public function __construct(public readonly int $id) {}
}
