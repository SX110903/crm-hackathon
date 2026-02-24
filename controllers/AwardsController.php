<?php
declare(strict_types=1);

require_once ROOT_PATH . '/models/AwardModel.php';

class AwardsController extends BaseController
{
    private AwardModel $awardModel;

    public function __construct()
    {
        parent::__construct();
        $this->awardModel = new AwardModel();
    }

    public function index(?int $id): void
    {
        $currentPage = $this->currentPage();
        $awards      = $this->awardModel->findAll($currentPage);
        $pagination  = $this->buildPagination($this->awardModel->count(), $currentPage);

        $this->render('awards/index', compact('awards', 'pagination'));
    }

    public function show(?int $id): void
    {
        $this->requireId($id, $this->url('awards'));

        $award = $this->awardModel->findById($id);
        if (!$award) {
            $this->setFlash('error', 'Premio no encontrado.');
            $this->redirect($this->url('awards'));
        }

        $this->render('awards/show', compact('award'));
    }

    public function create(?int $id): void
    {
        $this->render('awards/create');
    }

    public function store(?int $id): void
    {
        $this->requirePost($this->url('awards', 'create'));

        $data = [
            'awardName' => $this->post('award_name'),
            'category'  => $this->post('category'),
            'prize'     => $this->post('prize'),
        ];

        $errors = $this->awardModel->validate($data);
        if ($errors) {
            $this->setFlash('error', implode('<br>', $errors));
            $this->redirect($this->url('awards', 'create'));
        }

        try {
            $newId = $this->awardModel->create($data);
            $this->setFlash('success', 'Premio creado correctamente.');
            $this->redirect($this->url('awards', 'show', $newId));
        } catch (\PDOException) {
            $this->setFlash('error', 'Error al guardar el premio.');
            $this->redirect($this->url('awards', 'create'));
        }
    }

    // ─── GET: Formulario de asignación ──────────────────────────────────────────
    public function assign(?int $id): void
    {
        $this->requireId($id, $this->url('awards'));

        $award    = $this->awardModel->findById($id);
        if (!$award) {
            $this->setFlash('error', 'Premio no encontrado.');
            $this->redirect($this->url('awards'));
        }

        $projects = $this->awardModel->getEligibleProjects();

        $this->render('awards/assign', compact('award', 'projects'));
    }

    // ─── POST+PUT: Guardar asignación ────────────────────────────────────────────
    public function update(?int $id): void
    {
        $this->requireId($id, $this->url('awards'));
        $this->requirePut($this->url('awards', 'assign', $id));

        $data = [
            'projectId'   => $this->postInt('project_id'),
            'awardedDate' => $this->postDate('awarded_date') ?? date('Y-m-d'),
        ];

        $errors = $this->awardModel->validateAssign($data);
        if ($errors) {
            $this->setFlash('error', implode('<br>', $errors));
            $this->redirect($this->url('awards', 'assign', $id));
        }

        try {
            $this->awardModel->assign($id, $data['projectId'], $data['awardedDate']);
            $this->setFlash('success', 'Premio asignado correctamente.');
            $this->redirect($this->url('awards'));
        } catch (\PDOException) {
            $this->setFlash('error', 'Error al asignar el premio.');
            $this->redirect($this->url('awards', 'assign', $id));
        }
    }

    // ─── POST: Desasignar premio ──────────────────────────────────────────────────
    public function delete(?int $id): void
    {
        $this->requireId($id, $this->url('awards'));
        $this->requirePost($this->url('awards'));

        try {
            $this->awardModel->unassign($id);
            $this->setFlash('success', 'Asignación del premio eliminada.');
        } catch (\PDOException) {
            $this->setFlash('error', 'Error al desasignar el premio.');
        }

        $this->redirect($this->url('awards'));
    }
}
