<?php

declare(strict_types=1);

namespace App\Application\Queries\Mentor;

use App\Application\CQRS\Contracts\QueryInterface;

final class GetMentorByIdQuery implements QueryInterface
{
    public function __construct(public readonly int $id) {}
}
