<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

final class EventResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'            => $this->id,
            'name'          => $this->name,
            'description'   => $this->description,
            'start_date'    => $this->start_date?->format('Y-m-d'),
            'end_date'      => $this->end_date?->format('Y-m-d'),
            'location'      => $this->location,
            'max_teams'     => $this->max_teams,
            'category_type' => $this->category_type,
            'status'        => $this->status,
            'teams_count'   => $this->teams()->count(),
            'created_at'    => $this->created_at,
        ];
    }
}
