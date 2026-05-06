<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

final class ProjectResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'               => $this->id,
            'team_id'          => $this->team_id,
            'name'             => $this->name,
            'description'      => $this->description,
            'category'         => $this->category,
            'technology_stack' => $this->technology_stack,
            'github_url'       => $this->github_url,
            'demo_url'         => $this->demo_url,
            'status'           => $this->status,
            'created_at'       => $this->created_at,
            'updated_at'       => $this->updated_at,
        ];
    }
}
