<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

final class Evaluation extends Model
{
    use HasFactory;

    protected $table = 'evaluations';

    protected $fillable = [
        'project_id',
        'judge_id',
        'innovation_score',
        'technical_score',
        'presentation_score',
        'usability_score',
        'comments',
    ];

    protected $casts = [
        'innovation_score'    => 'float',
        'technical_score'     => 'float',
        'presentation_score'  => 'float',
        'usability_score'     => 'float',
    ];

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function judge(): BelongsTo
    {
        return $this->belongsTo(Judge::class);
    }

    public function getTotalScoreAttribute(): float
    {
        return ($this->innovation_score + $this->technical_score + $this->presentation_score + $this->usability_score) / 4;
    }
}
