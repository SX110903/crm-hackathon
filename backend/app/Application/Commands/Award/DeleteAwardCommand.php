<?php

declare(strict_types=1);

namespace App\Application\Commands\Award;

use App\Application\CQRS\Contracts\CommandInterface;

final class DeleteAwardCommand implements CommandInterface
{
    public function __construct(public readonly int $id) {}
}
