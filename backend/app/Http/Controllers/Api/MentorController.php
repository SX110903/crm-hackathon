<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Application\Commands\Mentor\CreateMentorCommand;
use App\Application\Commands\Mentor\DeleteMentorCommand;
use App\Application\Commands\Mentor\UpdateMentorCommand;
use App\Application\CQRS\CommandBus;
use App\Application\CQRS\QueryBus;
use App\Application\Queries\Mentor\GetMentorByIdQuery;
use App\Application\Queries\Mentor\GetMentorsQuery;
use App\Http\Resources\MentorResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

final class MentorController extends BaseApiController
{
    public function __construct(
        private readonly CommandBus $commandBus,
        private readonly QueryBus $queryBus,
    ) {}

    public function index(Request $request): JsonResponse
    {
        $result = $this->queryBus->dispatch(new GetMentorsQuery(
            page:    (int) $request->get('page', 1),
            perPage: (int) $request->get('per_page', 15),
        ));

        return $this->success(MentorResource::collection($result));
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'first_name'      => ['required', 'string', 'max:255'],
            'last_name'       => ['required', 'string', 'max:255'],
            'email'           => ['required', 'email', 'max:255', function ($attribute, $value, $fail) {
                if (DB::table('mentors')->where('email_hash', hash('sha256', strtolower(trim($value))))->exists()) {
                    $fail('The email has already been taken.');
                }
            }],
            'company'         => ['nullable', 'string', 'max:255'],
            'specialization'  => ['nullable', 'string', 'max:255'],
            'available_slots' => ['nullable', 'integer', 'min:0'],
        ]);

        $mentor = $this->commandBus->dispatch(new CreateMentorCommand(
            firstName:      $data['first_name'],
            lastName:       $data['last_name'],
            email:          $data['email'],
            company:        $data['company'] ?? null,
            specialization: $data['specialization'] ?? null,
            availableSlots: isset($data['available_slots']) ? (int) $data['available_slots'] : null,
        ));

        return $this->success(new MentorResource($mentor), 201);
    }

    public function show(int $id): JsonResponse
    {
        $mentor = $this->queryBus->dispatch(new GetMentorByIdQuery($id));

        return $this->success(new MentorResource($mentor));
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $data = $request->validate([
            'first_name'      => ['required', 'string', 'max:255'],
            'last_name'       => ['required', 'string', 'max:255'],
            'email'           => ['required', 'email', 'max:255', function ($attribute, $value, $fail) use ($id) {
                if (DB::table('mentors')->where('email_hash', hash('sha256', strtolower(trim($value))))->where('id', '!=', $id)->exists()) {
                    $fail('The email has already been taken.');
                }
            }],
            'company'         => ['nullable', 'string', 'max:255'],
            'specialization'  => ['nullable', 'string', 'max:255'],
            'available_slots' => ['nullable', 'integer', 'min:0'],
        ]);

        $mentor = $this->commandBus->dispatch(new UpdateMentorCommand(
            id:             $id,
            firstName:      $data['first_name'],
            lastName:       $data['last_name'],
            email:          $data['email'],
            company:        $data['company'] ?? null,
            specialization: $data['specialization'] ?? null,
            availableSlots: isset($data['available_slots']) ? (int) $data['available_slots'] : null,
        ));

        return $this->success(new MentorResource($mentor));
    }

    public function destroy(int $id): JsonResponse
    {
        $this->commandBus->dispatch(new DeleteMentorCommand($id));

        return $this->noContent();
    }
}
