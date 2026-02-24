<?php
declare(strict_types=1);

require_once ROOT_PATH . '/models/TeamModel.php';
require_once ROOT_PATH . '/models/ParticipantModel.php';

class TeamsController extends BaseController
{
    private TeamModel        $teamModel;
    private ParticipantModel $participantModel;

    public function __construct()
    {
        parent::__construct();
        $this->teamModel        = new TeamModel();
        $this->participantModel = new ParticipantModel();
    }

    // ─── GET: Listado con búsqueda ───────────────────────────────────────────────
    public function index(?int $id): void
    {
        $currentPage = $this->currentPage();
        $search      = trim((string) ($_GET['search'] ?? ''));

        if ($search !== '') {
            $teams      = $this->teamModel->search($search, $currentPage);
            $pagination = $this->buildPagination(
                $this->teamModel->countSearch($search),
                $currentPage
            );
        } else {
            $teams      = $this->teamModel->findAll($currentPage);
            $pagination = $this->buildPagination(
                $this->teamModel->count(),
                $currentPage
            );
        }

        $this->render('teams/index', compact('teams', 'pagination', 'search'));
    }

    // ─── GET: Detalle ────────────────────────────────────────────────────────────
    public function show(?int $id): void
    {
        $this->requireId($id, $this->url('teams'));

        $team = $this->teamModel->findById($id);
        if (!$team) {
            $this->setFlash('error', 'Equipo no encontrado.');
            $this->redirect($this->url('teams'));
        }

        $members  = $this->teamModel->getMembers($id);
        $project  = $this->teamModel->getProject($id);
        $sessions = $this->teamModel->getSessions($id);

        $this->render('teams/show', compact('team', 'members', 'project', 'sessions'));
    }

    // ─── GET: Formulario de creación ─────────────────────────────────────────────
    public function create(?int $id): void
    {
        $participants = $this->teamModel->getAvailableLeaders();
        $this->render('teams/create', compact('participants'));
    }

    // ─── POST: Guardar nuevo equipo ──────────────────────────────────────────────
    public function store(?int $id): void
    {
        $this->requirePost($this->url('teams', 'create'));
        $this->validateCsrf($this->url('teams', 'create'));

        $data = [
            'teamName'   => $this->post('team_name'),
            'leaderId'   => $this->postInt('leader_id'),
            'maxMembers' => $this->postInt('max_members', 5),
        ];

        $errors = $this->teamModel->validate($data);
        if ($errors) {
            $this->setFlash('error', implode('<br>', $errors));
            $this->redirect($this->url('teams', 'create'));
        }

        try {
            $newTeamId = $this->teamModel->create($data);
            $this->setFlash('success', 'Equipo creado correctamente.');
            $this->redirect($this->url('teams', 'show', $newTeamId));
        } catch (\PDOException) {
            $this->setFlash('error', 'Error al guardar el equipo. Comprueba que el líder no tenga ya un equipo.');
            $this->redirect($this->url('teams', 'create'));
        }
    }

    // ─── GET: Formulario de edición ──────────────────────────────────────────────
    public function edit(?int $id): void
    {
        $this->requireId($id, $this->url('teams'));

        $team = $this->teamModel->findById($id);
        if (!$team) {
            $this->setFlash('error', 'Equipo no encontrado.');
            $this->redirect($this->url('teams'));
        }

        $this->render('teams/edit', compact('team'));
    }

    // ─── POST+PUT: Actualizar equipo ─────────────────────────────────────────────
    public function update(?int $id): void
    {
        $this->requireId($id, $this->url('teams'));
        $this->requirePut($this->url('teams', 'edit', $id));
        $this->validateCsrf($this->url('teams', 'edit', $id));

        $data = [
            'teamName'   => $this->post('team_name'),
            'maxMembers' => $this->postInt('max_members', 5),
        ];

        $errors = $this->teamModel->validate(array_merge($data, ['leaderId' => 1]));
        if ($errors) {
            $this->setFlash('error', implode('<br>', $errors));
            $this->redirect($this->url('teams', 'edit', $id));
        }

        try {
            $this->teamModel->update($id, $data);
            $this->setFlash('success', 'Equipo actualizado correctamente.');
            $this->redirect($this->url('teams', 'show', $id));
        } catch (\PDOException) {
            $this->setFlash('error', 'Error al actualizar el equipo.');
            $this->redirect($this->url('teams', 'edit', $id));
        }
    }

    // ─── POST: Eliminar equipo ───────────────────────────────────────────────────
    public function delete(?int $id): void
    {
        $this->requireId($id, $this->url('teams'));
        $this->requirePost($this->url('teams'));
        $this->validateCsrf($this->url('teams'));

        try {
            $this->teamModel->delete($id);
            $this->setFlash('success', 'Equipo eliminado.');
        } catch (\PDOException) {
            $this->setFlash('error', 'No se puede eliminar el equipo (tiene datos asociados).');
        }

        $this->redirect($this->url('teams'));
    }
}
