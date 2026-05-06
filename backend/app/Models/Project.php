<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

final class Project extends Model
{
    use HasFactory;

    protected $table = 'projects';

    protected $fillable = [
        'team_id',
        'name',
        'description',
        'category',
        'technology_stack',
        'github_url',
        'demo_url',
        'status',
    ];

    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }

    public function evaluations(): HasMany
    {
        return $this->hasMany(Evaluation::class);
    }

    public function awards(): HasMany
    {
        return $this->hasMany(Award::class);
    }
}
