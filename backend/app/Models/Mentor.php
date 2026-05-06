<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

final class Mentor extends Model
{
    use HasFactory;

    protected $table = 'mentors';

    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'company',
        'specialization',
        'available_slots',
    ];
}
