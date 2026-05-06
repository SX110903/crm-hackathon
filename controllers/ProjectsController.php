<?php
declare(strict_types=1);

require_once ROOT_PATH . '/models/ProjectModel.php';

class ProjectsController extends BaseController
{
    private ProjectModel $projectModel;

    public function __construct()
    {
        parent::__construct();
        $this->projectModel = new ProjectModel();
    }

    // ─── GET: Listado con ranking ESTO ES LO QUE VE EL USUARIO────────────────────────────────────────────────
    public function index(?int $id): void
    {
        $currentPage = $this->currentPage();
        $projects    = $this->projectModel->findAll($currentPage);
        $pagination  = $this->buildPagination($this->projectModel->count(), $currentPage);

        $this->render('projects/index', compact('projects', 'pagination'));
    }

    // ─── GET: Detalle con evaluaciones ───────────────────────────────────────────
    public function show(?int $id): void
    {
        $this->requireId($id, $this->url('projects'));

        $project     = $this->projectModel->findById($id);
        if (!$project) {
            $this->setFlash('error', 'Proyecto no encontrado.');
            $this->redirect($this->url('projects'));
        }

        $evaluations = $this->projectModel->getEvaluations($id);

        $this->render('projects/show', compact('project', 'evaluations'));
    }

    // ─── GET: Formulario de creación ─────────────────────────────────────────────
    public function create(?int $id): void
    {
        $teams      = $this->projectModel->getTeamsForSelect();
        $categories = PROJECT_CATEGORIES;
        $statuses   = PROJECT_STATUSES;

        $this->render('projects/create', compact('teams', 'categories', 'statuses'));
    }

    // ─── POST: Guardar proyecto aqui realmente hay un pequeño dilema es que todo esto esta verificado tenerlo en cuenta para posterior modificaciones ──────────────────────────────────────────────────
    public function store(?int $id): void
    {
        $this->requirePost($this->url('projects', 'create'));
        $this->validateCsrf($this->url('projects', 'create'));

        $data = [
            'teamId'          => $this->postInt('team_id'),
            'projectName'     => $this->post('project_name'),
            'description'     => $this->post('description'),
            'category'        => $this->post('category'),
            'technologyStack' => $this->post('technology_stack'),
            'githubUrl'       => $this->post('github_url'),
            'status'          => $this->post('status'),
        ];

        $errors = $this->projectModel->validate($data);
        if ($errors) {
            $this->setFlash('error', implode('<br>', $errors));
            $this->redirect($this->url('projects', 'create'));
        }

        try {
            $newId = $this->projectModel->create($data);
            $this->setFlash('success', 'Proyecto registrado correctamente.');
            $this->redirect($this->url('projects', 'show', $newId));
        } catch (\PDOException) {
            $this->setFlash('error', 'Error al guardar el proyecto.');
            $this->redirect($this->url('projects', 'create'));
        }
    }

    // ─── GET: Formulario de edición ──────────────────────────────────────────────
    public function edit(?int $id): void
    {
        $this->requireId($id, $this->url('projects'));

        $project    = $this->projectModel->findById($id);
        if (!$project) {
            $this->setFlash('error', 'Proyecto no encontrado.');
            $this->redirect($this->url('projects'));
        }

        $categories = PROJECT_CATEGORIES;
        $statuses   = PROJECT_STATUSES;

        $this->render('projects/edit', compact('project', 'categories', 'statuses'));
    }

    // ─── POST+PUT: Actualizar proyecto ───────────────────────────────────────────
    public function update(?int $id): void
    {
        $this->requireId($id, $this->url('projects'));
        $this->requirePut($this->url('projects', 'edit', $id));
        $this->validateCsrf($this->url('projects', 'edit', $id));

        $data = [
            'projectName'     => $this->post('project_name'),
            'description'     => $this->post('description'),
            'category'        => $this->post('category'),
            'technologyStack' => $this->post('technology_stack'),
            'githubUrl'       => $this->post('github_url'),
            'status'          => $this->post('status'),
        ];

        // Para validación de categoría/estado reutilizamos validate con teamId dummy
        $errors = $this->projectModel->validate(array_merge($data, ['teamId' => 1]));
        if ($errors) {
            $this->setFlash('error', implode('<br>', $errors));
            $this->redirect($this->url('projects', 'edit', $id));
        }

        try {
            $this->projectModel->update($id, $data);
            $this->setFlash('success', 'Proyecto actualizado correctamente.');
            $this->redirect($this->url('projects', 'show', $id));
        } catch (\PDOException) {
            $this->setFlash('error', 'Error al actualizar el proyecto.');
            $this->redirect($this->url('projects', 'edit', $id));
        }
    }

    // ─── POST: Eliminar proyecto ─────────────────────────────────────────────────
    public function delete(?int $id): void
    {
        $this->requireId($id, $this->url('projects'));
        $this->requirePost($this->url('projects'));
        $this->validateCsrf($this->url('projects'));

        try {
            $this->projectModel->delete($id);
            $this->setFlash('success', 'Proyecto eliminado.');
        } catch (\PDOException) {
            $this->setFlash('error', 'No se puede eliminar (tiene evaluaciones o premios asociados).');
        }

        $this->redirect($this->url('projects'));
    }
}
