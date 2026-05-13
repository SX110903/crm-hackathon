<?php

declare(strict_types=1);

namespace App\Models;

use App\Traits\HasHashedEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

final class Judge extends Model
{
    use HasFactory, HasHashedEmail;

    protected $table = 'judges';

    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'email_hash',
        'search_index',
        'company',
        'expertise',
        'years_of_experience',
    ];

    protected $casts = [
        'first_name' => 'encrypted',
        'last_name'  => 'encrypted',
        'email'      => 'encrypted',
        'company'    => 'encrypted',
    ];

    public function evaluations(): HasMany
    {
        return $this->hasMany(Evaluation::class);
    }
}
