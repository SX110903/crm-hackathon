<?php

declare(strict_types=1);

namespace App\Models;

use App\Traits\HasHashedEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

final class Mentor extends Model
{
    use HasFactory, HasHashedEmail;

    protected $table = 'mentors';

    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'email_hash',
        'search_index',
        'company',
        'specialization',
        'available_slots',
    ];

    protected $casts = [
        'first_name' => 'encrypted',
        'last_name'  => 'encrypted',
        'email'      => 'encrypted',
        'company'    => 'encrypted',
    ];
}
