<?php

declare(strict_types=1);

namespace App\Application\Commands\Mentor;

use App\Application\CQRS\Contracts\CommandInterface;

final class DeleteMentorCommand implements CommandInterface
{
    public function __construct(public readonly int $id) {}
}
