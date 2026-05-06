<?php

namespace App\Providers;

use App\Application\Commands\Award\AssignAwardCommand;
use App\Application\Commands\Award\AssignAwardHandler;
use App\Application\Commands\Award\CreateAwardCommand;
use App\Application\Commands\Award\CreateAwardHandler;
use App\Application\Commands\Award\DeleteAwardCommand;
use App\Application\Commands\Award\DeleteAwardHandler;
use App\Application\Commands\Award\UpdateAwardCommand;
use App\Application\Commands\Award\UpdateAwardHandler;
use App\Application\Commands\Evaluation\CreateEvaluationCommand;
use App\Application\Commands\Evaluation\CreateEvaluationHandler;
use App\Application\Commands\Judge\CreateJudgeCommand;
use App\Application\Commands\Judge\CreateJudgeHandler;
use App\Application\Commands\Judge\DeleteJudgeCommand;
use App\Application\Commands\Judge\DeleteJudgeHandler;
use App\Application\Commands\Judge\UpdateJudgeCommand;
use App\Application\Commands\Judge\UpdateJudgeHandler;
use App\Application\Commands\Mentor\CreateMentorCommand;
use App\Application\Commands\Mentor\CreateMentorHandler;
use App\Application\Commands\Mentor\DeleteMentorCommand;
use App\Application\Commands\Mentor\DeleteMentorHandler;
use App\Application\Commands\Mentor\UpdateMentorCommand;
use App\Application\Commands\Mentor\UpdateMentorHandler;
use App\Application\Commands\Participant\CreateParticipantCommand;
use App\Application\Commands\Participant\CreateParticipantHandler;
use App\Application\Commands\Participant\DeleteParticipantCommand;
use App\Application\Commands\Participant\DeleteParticipantHandler;
use App\Application\Commands\Participant\UpdateParticipantCommand;
use App\Application\Commands\Participant\UpdateParticipantHandler;
use App\Application\Commands\Project\CreateProjectCommand;
use App\Application\Commands\Project\CreateProjectHandler;
use App\Application\Commands\Project\DeleteProjectCommand;
use App\Application\Commands\Project\DeleteProjectHandler;
use App\Application\Commands\Project\UpdateProjectCommand;
use App\Application\Commands\Project\UpdateProjectHandler;
use App\Application\Commands\Team\AddTeamMemberCommand;
use App\Application\Commands\Team\AddTeamMemberHandler;
use App\Application\Commands\Team\CreateTeamCommand;
use App\Application\Commands\Team\CreateTeamHandler;
use App\Application\Commands\Team\DeleteTeamCommand;
use App\Application\Commands\Team\DeleteTeamHandler;
use App\Application\Commands\Team\RemoveTeamMemberCommand;
use App\Application\Commands\Team\RemoveTeamMemberHandler;
use App\Application\Commands\Team\UpdateTeamCommand;
use App\Application\Commands\Team\UpdateTeamHandler;
use App\Application\CQRS\CommandBus;
use App\Application\CQRS\QueryBus;
use App\Application\Queries\Award\GetAwardByIdQuery;
use App\Application\Queries\Award\GetAwardByIdHandler;
use App\Application\Queries\Award\GetAwardsQuery;
use App\Application\Queries\Award\GetAwardsHandler;
use App\Application\Queries\Dashboard\GetDashboardStatsQuery;
use App\Application\Queries\Dashboard\GetDashboardStatsHandler;
use App\Application\Queries\Evaluation\GetEvaluationByIdQuery;
use App\Application\Queries\Evaluation\GetEvaluationByIdHandler;
use App\Application\Queries\Evaluation\GetEvaluationsQuery;
use App\Application\Queries\Evaluation\GetEvaluationsHandler;
use App\Application\Queries\Judge\GetJudgeByIdQuery;
use App\Application\Queries\Judge\GetJudgeByIdHandler;
use App\Application\Queries\Judge\GetJudgesQuery;
use App\Application\Queries\Judge\GetJudgesHandler;
use App\Application\Queries\Mentor\GetMentorByIdQuery;
use App\Application\Queries\Mentor\GetMentorByIdHandler;
use App\Application\Queries\Mentor\GetMentorsQuery;
use App\Application\Queries\Mentor\GetMentorsHandler;
use App\Application\Queries\Participant\GetParticipantByIdQuery;
use App\Application\Queries\Participant\GetParticipantByIdHandler;
use App\Application\Queries\Participant\GetParticipantsQuery;
use App\Application\Queries\Participant\GetParticipantsHandler;
use App\Application\Queries\Project\GetProjectByIdQuery;
use App\Application\Queries\Project\GetProjectByIdHandler;
use App\Application\Queries\Project\GetProjectsQuery;
use App\Application\Queries\Project\GetProjectsHandler;
use App\Application\Queries\Team\GetTeamByIdQuery;
use App\Application\Queries\Team\GetTeamByIdHandler;
use App\Application\Queries\Team\GetTeamsQuery;
use App\Application\Queries\Team\GetTeamsHandler;
use App\Domain\Repositories\AwardRepositoryInterface;
use App\Domain\Repositories\EvaluationRepositoryInterface;
use App\Domain\Repositories\JudgeRepositoryInterface;
use App\Domain\Repositories\MentorRepositoryInterface;
use App\Domain\Repositories\ParticipantRepositoryInterface;
use App\Domain\Repositories\ProjectRepositoryInterface;
use App\Domain\Repositories\TeamRepositoryInterface;
use App\Infrastructure\Repositories\EloquentAwardRepository;
use App\Infrastructure\Repositories\EloquentEvaluationRepository;
use App\Infrastructure\Repositories\EloquentJudgeRepository;
use App\Infrastructure\Repositories\EloquentMentorRepository;
use App\Infrastructure\Repositories\EloquentParticipantRepository;
use App\Infrastructure\Repositories\EloquentProjectRepository;
use App\Infrastructure\Repositories\EloquentTeamRepository;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Register CQRS buses as singletons
        $this->app->singleton(CommandBus::class, function ($app) {
            return new CommandBus($app);
        });

        $this->app->singleton(QueryBus::class, function ($app) {
            return new QueryBus($app);
        });

        // Bind repository interfaces to their Eloquent implementations
        $this->app->bind(ParticipantRepositoryInterface::class, EloquentParticipantRepository::class);
        $this->app->bind(TeamRepositoryInterface::class, EloquentTeamRepository::class);
        $this->app->bind(ProjectRepositoryInterface::class, EloquentProjectRepository::class);
        $this->app->bind(JudgeRepositoryInterface::class, EloquentJudgeRepository::class);
        $this->app->bind(MentorRepositoryInterface::class, EloquentMentorRepository::class);
        $this->app->bind(EvaluationRepositoryInterface::class, EloquentEvaluationRepository::class);
        $this->app->bind(AwardRepositoryInterface::class, EloquentAwardRepository::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Register all command -> handler mappings
        $commandBus = $this->app->make(CommandBus::class);

        // Participant commands
        $commandBus->register(CreateParticipantCommand::class, CreateParticipantHandler::class);
        $commandBus->register(UpdateParticipantCommand::class, UpdateParticipantHandler::class);
        $commandBus->register(DeleteParticipantCommand::class, DeleteParticipantHandler::class);

        // Team commands
        $commandBus->register(CreateTeamCommand::class, CreateTeamHandler::class);
        $commandBus->register(UpdateTeamCommand::class, UpdateTeamHandler::class);
        $commandBus->register(DeleteTeamCommand::class, DeleteTeamHandler::class);
        $commandBus->register(AddTeamMemberCommand::class, AddTeamMemberHandler::class);
        $commandBus->register(RemoveTeamMemberCommand::class, RemoveTeamMemberHandler::class);

        // Project commands
        $commandBus->register(CreateProjectCommand::class, CreateProjectHandler::class);
        $commandBus->register(UpdateProjectCommand::class, UpdateProjectHandler::class);
        $commandBus->register(DeleteProjectCommand::class, DeleteProjectHandler::class);

        // Judge commands
        $commandBus->register(CreateJudgeCommand::class, CreateJudgeHandler::class);
        $commandBus->register(UpdateJudgeCommand::class, UpdateJudgeHandler::class);
        $commandBus->register(DeleteJudgeCommand::class, DeleteJudgeHandler::class);

        // Mentor commands
        $commandBus->register(CreateMentorCommand::class, CreateMentorHandler::class);
        $commandBus->register(UpdateMentorCommand::class, UpdateMentorHandler::class);
        $commandBus->register(DeleteMentorCommand::class, DeleteMentorHandler::class);

        // Evaluation commands
        $commandBus->register(CreateEvaluationCommand::class, CreateEvaluationHandler::class);

        // Award commands
        $commandBus->register(CreateAwardCommand::class, CreateAwardHandler::class);
        $commandBus->register(UpdateAwardCommand::class, UpdateAwardHandler::class);
        $commandBus->register(DeleteAwardCommand::class, DeleteAwardHandler::class);
        $commandBus->register(AssignAwardCommand::class, AssignAwardHandler::class);

        // Register all query -> handler mappings
        $queryBus = $this->app->make(QueryBus::class);

        // Participant queries
        $queryBus->register(GetParticipantsQuery::class, GetParticipantsHandler::class);
        $queryBus->register(GetParticipantByIdQuery::class, GetParticipantByIdHandler::class);

        // Team queries
        $queryBus->register(GetTeamsQuery::class, GetTeamsHandler::class);
        $queryBus->register(GetTeamByIdQuery::class, GetTeamByIdHandler::class);

        // Project queries
        $queryBus->register(GetProjectsQuery::class, GetProjectsHandler::class);
        $queryBus->register(GetProjectByIdQuery::class, GetProjectByIdHandler::class);

        // Judge queries
        $queryBus->register(GetJudgesQuery::class, GetJudgesHandler::class);
        $queryBus->register(GetJudgeByIdQuery::class, GetJudgeByIdHandler::class);

        // Mentor queries
        $queryBus->register(GetMentorsQuery::class, GetMentorsHandler::class);
        $queryBus->register(GetMentorByIdQuery::class, GetMentorByIdHandler::class);

        // Evaluation queries
        $queryBus->register(GetEvaluationsQuery::class, GetEvaluationsHandler::class);
        $queryBus->register(GetEvaluationByIdQuery::class, GetEvaluationByIdHandler::class);

        // Award queries
        $queryBus->register(GetAwardsQuery::class, GetAwardsHandler::class);
        $queryBus->register(GetAwardByIdQuery::class, GetAwardByIdHandler::class);

        // Dashboard queries
        $queryBus->register(GetDashboardStatsQuery::class, GetDashboardStatsHandler::class);
    }
}
