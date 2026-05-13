<?php

declare(strict_types=1);

namespace App\Application\Commands\Project;

use App\Application\CQRS\Contracts\CommandInterface;

final class UpdateProjectCommand implements CommandInterface
{
    public function __construct(
        public readonly int $id,
        public readonly string $name,
        public readonly ?string $description,
        public readonly ?string $category,
        public readonly ?string $technologyStack,
        public readonly ?string $githubUrl,
        public readonly ?string $demoUrl,
        public readonly string $status,
    ) {}
}
