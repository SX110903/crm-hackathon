<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Models\Event;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Resources\EventResource;

final class EventController extends BaseApiController
{
    public function index(Request $request): JsonResponse
    {
        $events = Event::query()
            ->when($request->get('status'), fn($q, $s) => $q->where('status', $s))
            ->orderBy('start_date', 'desc')
            ->paginate((int) $request->get('per_page', 50));

        return $this->success(EventResource::collection($events));
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'name'          => ['required', 'string', 'max:255'],
            'description'   => ['nullable', 'string'],
            'start_date'    => ['required', 'date'],
            'end_date'      => ['required', 'date', 'after_or_equal:start_date'],
            'location'      => ['nullable', 'string', 'max:255'],
            'max_teams'     => ['nullable', 'integer', 'min:1'],
            'category_type' => ['nullable', 'in:general,red_vs_blue,mixed'],
            'status'        => ['nullable', 'in:upcoming,active,closed'],
        ]);

        $event = Event::create([
            'name'          => $data['name'],
            'description'   => $data['description'] ?? null,
            'start_date'    => $data['start_date'],
            'end_date'      => $data['end_date'],
            'location'      => $data['location'] ?? null,
            'max_teams'     => (int) ($data['max_teams'] ?? 50),
            'category_type' => $data['category_type'] ?? 'general',
            'status'        => $data['status'] ?? 'upcoming',
        ]);

        return $this->success(new EventResource($event), 201);
    }

    public function show(int $id): JsonResponse
    {
        $event = Event::findOrFail($id);
        return $this->success(new EventResource($event));
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $event = Event::findOrFail($id);

        $data = $request->validate([
            'name'          => ['required', 'string', 'max:255'],
            'description'   => ['nullable', 'string'],
            'start_date'    => ['required', 'date'],
            'end_date'      => ['required', 'date'],
            'location'      => ['nullable', 'string', 'max:255'],
            'max_teams'     => ['nullable', 'integer', 'min:1'],
            'category_type' => ['nullable', 'in:general,red_vs_blue,mixed'],
            'status'        => ['nullable', 'in:upcoming,active,closed'],
        ]);

        $event->update($data);

        return $this->success(new EventResource($event));
    }

    public function destroy(int $id): JsonResponse
    {
        Event::findOrFail($id)->delete();
        return $this->noContent();
    }

    public function active(): JsonResponse
    {
        $events = Event::where('status', 'active')
            ->orderBy('start_date')
            ->get();

        return $this->success(EventResource::collection($events));
    }
}
