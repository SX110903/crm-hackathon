<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

final class Judge extends Model
{
    use HasFactory;

    protected $table = 'judges';

    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'company',
        'expertise',
        'years_of_experience',
    ];

    public function evaluations(): HasMany
    {
        return $this->hasMany(Evaluation::class);
    }
}
