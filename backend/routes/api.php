<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ParticipantController;
use App\Http\Controllers\Api\TeamController;
use App\Http\Controllers\Api\ProjectController;
use App\Http\Controllers\Api\JudgeController;
use App\Http\Controllers\Api\MentorController;
use App\Http\Controllers\Api\EvaluationController;
use App\Http\Controllers\Api\AwardController;
use App\Http\Controllers\Api\DashboardController;

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

    Route::apiResource('teams', TeamController::class);
    Route::get('teams/{id}/members', [TeamController::class, 'members']);
    Route::post('teams/{id}/members', [TeamController::class, 'addMember']);
    Route::delete('teams/{id}/members/{participantId}', [TeamController::class, 'removeMember']);
});
