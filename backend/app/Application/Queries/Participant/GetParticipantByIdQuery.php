<?php

declare(strict_types=1);

namespace App\Application\Queries\Participant;

use App\Application\CQRS\Contracts\QueryInterface;

final class GetParticipantByIdQuery implements QueryInterface
{
    public function __construct(public readonly int $id) {}
}
