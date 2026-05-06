<?php

declare(strict_types=1);

namespace App\Application\Commands\Project;

use App\Application\CQRS\Contracts\CommandInterface;

final class DeleteProjectCommand implements CommandInterface
{
    public function __construct(public readonly int $id) {}
}
