<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

final class Award extends Model
{
    use HasFactory;

    protected $table = 'awards';

    protected $fillable = [
        'name',
        'category',
        'prize',
        'project_id',
        'awarded_date',
    ];

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }
}
