<?php

declare(strict_types=1);

namespace App\Application\Commands\Award;

use App\Application\CQRS\Contracts\CommandInterface;

final class CreateAwardCommand implements CommandInterface
{
    public function __construct(
        public readonly string $name,
        public readonly ?string $category,
        public readonly ?string $prize,
        public readonly ?int $projectId,
    ) {}
}
