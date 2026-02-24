<?php
declare(strict_types=1);

require_once ROOT_PATH . '/models/JudgeModel.php';

class JudgesController extends BaseController
{
    private JudgeModel $judgeModel;

    public function __construct()
    {
        parent::__construct();
        $this->judgeModel = new JudgeModel();
    }

    public function index(?int $id): void
    {
        $currentPage = $this->currentPage();
        $judges      = $this->judgeModel->findAll($currentPage);
        $pagination  = $this->buildPagination($this->judgeModel->count(), $currentPage);

        $this->render('judges/index', compact('judges', 'pagination'));
    }

    public function show(?int $id): void
    {
        $this->requireId($id, $this->url('judges'));

        $judge       = $this->judgeModel->findById($id);
        if (!$judge) {
            $this->setFlash('error', 'Juez no encontrado.');
            $this->redirect($this->url('judges'));
        }

        $evaluations = $this->judgeModel->getEvaluations($id);

        $this->render('judges/show', compact('judge', 'evaluations'));
    }

    public function create(?int $id): void
    {
        $this->render('judges/create');
    }

    public function store(?int $id): void
    {
        $this->requirePost($this->url('judges', 'create'));
        $this->validateCsrf($this->url('judges', 'create'));

        $data = [
            'firstName'         => $this->post('first_name'),
            'lastName'          => $this->post('last_name'),
            'email'             => $this->post('email'),
            'company'           => $this->post('company'),
            'expertise'         => $this->post('expertise'),
            'yearsOfExperience' => $this->postInt('years_of_experience', 0),
        ];

        $errors = $this->judgeModel->validate($data);
        if ($errors) {
            $this->setFlash('error', implode('<br>', $errors));
            $this->redirect($this->url('judges', 'create'));
        }

        try {
            $newId = $this->judgeModel->create($data);
            $this->setFlash('success', 'Juez registrado correctamente.');
            $this->redirect($this->url('judges', 'show', $newId));
        } catch (\PDOException) {
            $this->setFlash('error', 'Error al guardar el juez. El email puede estar duplicado.');
            $this->redirect($this->url('judges', 'create'));
        }
    }

    public function edit(?int $id): void
    {
        $this->requireId($id, $this->url('judges'));

        $judge = $this->judgeModel->findById($id);
        if (!$judge) {
            $this->setFlash('error', 'Juez no encontrado.');
            $this->redirect($this->url('judges'));
        }

        $this->render('judges/edit', compact('judge'));
    }

    public function update(?int $id): void
    {
        $this->requireId($id, $this->url('judges'));
        $this->requirePut($this->url('judges', 'edit', $id));
        $this->validateCsrf($this->url('judges', 'edit', $id));

        $data = [
            'firstName'         => $this->post('first_name'),
            'lastName'          => $this->post('last_name'),
            'email'             => $this->post('email'),
            'company'           => $this->post('company'),
            'expertise'         => $this->post('expertise'),
            'yearsOfExperience' => $this->postInt('years_of_experience', 0),
        ];

        $errors = $this->judgeModel->validate($data);
        if ($errors) {
            $this->setFlash('error', implode('<br>', $errors));
            $this->redirect($this->url('judges', 'edit', $id));
        }

        try {
            $this->judgeModel->update($id, $data);
            $this->setFlash('success', 'Juez actualizado correctamente.');
            $this->redirect($this->url('judges', 'show', $id));
        } catch (\PDOException) {
            $this->setFlash('error', 'Error al actualizar el juez.');
            $this->redirect($this->url('judges', 'edit', $id));
        }
    }

    public function delete(?int $id): void
    {
        $this->requireId($id, $this->url('judges'));
        $this->requirePost($this->url('judges'));
        $this->validateCsrf($this->url('judges'));

        try {
            $this->judgeModel->delete($id);
            $this->setFlash('success', 'Juez eliminado.');
        } catch (\PDOException) {
            $this->setFlash('error', 'No se puede eliminar (tiene evaluaciones registradas).');
        }

        $this->redirect($this->url('judges'));
    }
}
