<?php

declare(strict_types=1);

namespace App\Application\Commands\Participant;

use App\Application\CQRS\Contracts\CommandInterface;

final class RegisterParticipantCommand implements CommandInterface
{
    public function __construct(
        public readonly string $firstName,
        public readonly string $lastName,
        public readonly string $email,
        public readonly string $password,
        public readonly ?string $phone = null,
        public readonly ?string $university = null,
        public readonly ?string $major = null,
        public readonly ?int $yearOfStudy = null,
    ) {}
}
