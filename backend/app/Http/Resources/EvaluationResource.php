<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

final class EvaluationResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'                  => $this->id,
            'project_id'          => $this->project_id,
            'judge_id'            => $this->judge_id,
            'innovation_score'    => $this->innovation_score,
            'technical_score'     => $this->technical_score,
            'presentation_score'  => $this->presentation_score,
            'usability_score'     => $this->usability_score,
            'total_score'         => ($this->innovation_score + $this->technical_score + $this->presentation_score + $this->usability_score) / 4,
            'comments'            => $this->comments,
            'created_at'          => $this->created_at,
            'updated_at'          => $this->updated_at,
        ];
    }
}
