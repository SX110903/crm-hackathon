<?php

declare(strict_types=1);

namespace App\Traits;

trait HasHashedEmail
{
    public static function bootHasHashedEmail(): void
    {
        static::saving(function ($model) {
            if ($model->email !== null) {
                $model->email_hash = hash('sha256', strtolower(trim($model->email)));
            }
        });
    }

    public static function findByEmail(string $email): ?static
    {
        return static::where('email_hash', hash('sha256', strtolower(trim($email))))->first();
    }
}
