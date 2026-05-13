<?php

declare(strict_types=1);

namespace App\Models;

use App\Traits\HasHashedEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

final class Participant extends Authenticatable
{
    use HasApiTokens, HasFactory, HasHashedEmail, Notifiable;

    protected $table = 'participants';

    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'email_hash',
        'search_index',
        'phone',
        'university',
        'major',
        'year_of_study',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'first_name'        => 'encrypted',
        'last_name'         => 'encrypted',
        'email'             => 'encrypted',
        'phone'             => 'encrypted',
        'university'        => 'encrypted',
        'major'             => 'encrypted',
        'email_verified_at' => 'datetime',
        'password'          => 'hashed',
    ];

    public function teamMemberships(): HasMany
    {
        return $this->hasMany(TeamMember::class);
    }
}
