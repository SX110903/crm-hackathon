<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ParticipantAuthController;
use App\Http\Controllers\Api\ParticipantController;
use App\Http\Controllers\Api\TeamController;
use App\Http\Controllers\Api\ProjectController;
use App\Http\Controllers\Api\JudgeController;
use App\Http\Controllers\Api\MentorController;
use App\Http\Controllers\Api\EvaluationController;
use App\Http\Controllers\Api\AwardController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\EventController;
use App\Http\Controllers\Api\RegistrationController;

// Public endpoints — no auth required
Route::get('events/active', [EventController::class, 'active']);
Route::post('register', [RegistrationController::class, 'store']);

// Public participant auth
Route::prefix('participant')->group(function () {
    Route::post('/register', [ParticipantAuthController::class, 'register']);
    Route::post('/login', [ParticipantAuthController::class, 'login']);
});

// Protected participant routes
Route::prefix('participant')->middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [ParticipantAuthController::class, 'logout']);
    Route::get('/me', [ParticipantAuthController::class, 'me']);
    Route::put('/profile', [ParticipantAuthController::class, 'updateProfile']);
    Route::put('/change-password', [ParticipantAuthController::class, 'changePassword']);
    Route::get('/my-team', [ParticipantAuthController::class, 'myTeam']);
    Route::get('/my-project', [ParticipantAuthController::class, 'myProject']);
    Route::get('/my-events', [ParticipantAuthController::class, 'myEvents']);
});

Route::prefix('auth')->group(function () {
    Route::post('login', [AuthController::class, 'login']);

    Route::middleware('auth:sanctum')->group(function () {
        Route::post('logout', [AuthController::class, 'logout']);
        Route::get('me', [AuthController::class, 'me']);
    });
});

Route::middleware('auth:sanctum')->group(function () {
    Route::get('dashboard', [DashboardController::class, 'index']);

    Route::apiResource('participants', ParticipantController::class);
    Route::apiResource('projects', ProjectController::class);
    Route::apiResource('judges', JudgeController::class);
    Route::apiResource('mentors', MentorController::class);

    Route::apiResource('evaluations', EvaluationController::class)->only(['index', 'store', 'show']);

    Route::apiResource('awards', AwardController::class);
    Route::post('awards/{id}/assign', [AwardController::class, 'assign']);

    Route::apiResource('events', EventController::class);

    Route::apiResource('teams', TeamController::class);
    Route::get('teams/{id}/members', [TeamController::class, 'members']);
    Route::post('teams/{id}/members', [TeamController::class, 'addMember']);
    Route::delete('teams/{id}/members/{participantId}', [TeamController::class, 'removeMember']);
});
