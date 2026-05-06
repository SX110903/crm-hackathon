<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

final class Participant extends Model
{
    use HasFactory;

    protected $table = 'participants';

    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'phone',
        'university',
        'major',
        'year_of_study',
    ];

    public function teamMemberships(): HasMany
    {
        return $this->hasMany(TeamMember::class);
    }
}
