<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Application\Commands\Participant\ChangeParticipantPasswordCommand;
use App\Application\Commands\Participant\RegisterParticipantCommand;
use App\Application\Commands\Participant\UpdateParticipantProfileCommand;
use App\Application\CQRS\CommandBus;
use App\Models\Event;
use App\Models\Participant;
use App\Models\Team;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

final class ParticipantAuthController extends BaseApiController
{
    public function __construct(private readonly CommandBus $commandBus) {}

    public function register(Request $request): JsonResponse
    {
        $data = $request->validate([
            'first_name'    => ['required', 'string', 'max:255'],
            'last_name'     => ['required', 'string', 'max:255'],
            'email'         => ['required', 'email', 'max:255'],
            'password'      => ['required', 'confirmed', Password::min(8)],
            'phone'         => ['nullable', 'string', 'max:50'],
            'university'    => ['nullable', 'string', 'max:255'],
            'major'         => ['nullable', 'string', 'max:255'],
            'year_of_study' => ['nullable', 'integer', 'min:1', 'max:10'],
        ]);

        if (Participant::findByEmail($data['email'])) {
            return $this->error('Email already registered.', 422);
        }

        $participant = $this->commandBus->dispatch(new RegisterParticipantCommand(
            firstName:   $data['first_name'],
            lastName:    $data['last_name'],
            email:       $data['email'],
            password:    $data['password'],
            phone:       $data['phone'] ?? null,
            university:  $data['university'] ?? null,
            major:       $data['major'] ?? null,
            yearOfStudy: $data['year_of_study'] ?? null,
        ));

        $token = $participant->createToken('participant-token')->plainTextToken;

        return $this->success(['token' => $token, 'participant' => $participant], 201);
    }

    public function login(Request $request): JsonResponse
    {
        $data = $request->validate([
            'email'    => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        $participant = Participant::findByEmail($data['email']);

        if (!$participant || !Hash::check($data['password'], $participant->password)) {
            return $this->error('Invalid credentials.', 401);
        }

        $token = $participant->createToken('participant-token')->plainTextToken;

        return $this->success(['token' => $token, 'participant' => $participant]);
    }

    public function logout(Request $request): JsonResponse
    {
        optional($request->user()->currentAccessToken())->delete();

        return $this->noContent();
    }

    public function me(Request $request): JsonResponse
    {
        return $this->success($request->user());
    }

    public function updateProfile(Request $request): JsonResponse
    {
        $data = $request->validate([
            'first_name'    => ['required', 'string', 'max:255'],
            'last_name'     => ['required', 'string', 'max:255'],
            'phone'         => ['nullable', 'string', 'max:50'],
            'university'    => ['nullable', 'string', 'max:255'],
            'major'         => ['nullable', 'string', 'max:255'],
            'year_of_study' => ['nullable', 'integer', 'min:1', 'max:10'],
        ]);

        $participant = $this->commandBus->dispatch(new UpdateParticipantProfileCommand(
            id:          $request->user()->id,
            firstName:   $data['first_name'],
            lastName:    $data['last_name'],
            phone:       $data['phone'] ?? null,
            university:  $data['university'] ?? null,
            major:       $data['major'] ?? null,
            yearOfStudy: $data['year_of_study'] ?? null,
        ));

        return $this->success($participant);
    }

    public function changePassword(Request $request): JsonResponse
    {
        $data = $request->validate([
            'current_password' => ['required', 'string'],
            'password'         => ['required', 'confirmed', Password::min(8)],
        ]);

        if (!Hash::check($data['current_password'], $request->user()->password)) {
            return $this->error('Current password is incorrect.', 422);
        }

        $participant = $this->commandBus->dispatch(new ChangeParticipantPasswordCommand(
            id:          $request->user()->id,
            newPassword: $data['password'],
        ));

        return $this->success($participant);
    }

    public function myTeam(Request $request): JsonResponse
    {
        $membership = $request->user()
            ->teamMemberships()
            ->with('team')
            ->first();

        return $this->success($membership?->team);
    }

    public function myProject(Request $request): JsonResponse
    {
        $teamId = $request->user()->teamMemberships()->value('team_id');

        if (!$teamId) {
            return $this->success(null);
        }

        $project = \App\Models\Project::where('team_id', $teamId)->first();

        return $this->success($project);
    }

    public function myEvents(Request $request): JsonResponse
    {
        $teamIds = $request->user()->teamMemberships()->pluck('team_id');

        if ($teamIds->isEmpty()) {
            return $this->success([]);
        }

        $eventIds = Team::whereIn('id', $teamIds)
            ->whereNotNull('event_id')
            ->pluck('event_id')
            ->unique();

        $events = Event::whereIn('id', $eventIds)->get();

        return $this->success($events);
    }
}
