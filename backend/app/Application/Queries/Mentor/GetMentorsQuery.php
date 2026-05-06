<?php

declare(strict_types=1);

namespace App\Application\Queries\Mentor;

use App\Application\CQRS\Contracts\QueryInterface;

final class GetMentorsQuery implements QueryInterface
{
    public function __construct(
        public readonly int $page = 1,
        public readonly int $perPage = 15,
    ) {}
}
