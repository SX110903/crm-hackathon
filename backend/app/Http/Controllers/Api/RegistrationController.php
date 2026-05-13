<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Models\Participant;
use App\Models\Team;
use App\Models\TeamMember;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

final class RegistrationController extends BaseApiController
{
    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'event_id'        => ['required', 'integer', 'exists:events,id'],
            'team_name'       => ['required', 'string', 'max:255', 'unique:teams,name'],
            'category'        => ['required', 'in:red,blue'],
            'members'         => ['required', 'array', 'min:1', 'max:5'],
            'members.*.name'  => ['required', 'string', 'max:255'],
            'members.*.email' => ['required', 'email', 'max:255', function ($attribute, $value, $fail) {
                if (DB::table('participants')->where('email_hash', hash('sha256', strtolower(trim($value))))->exists()) {
                    $fail('The email has already been taken.');
                }
            }],
            'members.*.role'  => ['required', 'string', 'max:100'],
        ]);

        DB::transaction(function () use ($data) {
            $team = Team::create([
                'name'        => $data['team_name'],
                'category'    => $data['category'],
                'event_id'    => (int) $data['event_id'],
                'max_members' => 5,
            ]);

            foreach ($data['members'] as $index => $memberData) {
                $parts     = explode(' ', trim($memberData['name']), 2);
                $firstName = $parts[0];
                $lastName  = $parts[1] ?? '-';

                $participant = Participant::create([
                    'first_name' => $firstName,
                    'last_name'  => $lastName,
                    'email'      => $memberData['email'],
                ]);

                TeamMember::create([
                    'team_id'        => $team->id,
                    'participant_id' => $participant->id,
                    'role'           => $memberData['role'],
                ]);

                if ($index === 0) {
                    $team->update(['leader_id' => $participant->id]);
                }
            }
        });

        return $this->success(['message' => 'Team registered successfully'], 201);
    }
}
