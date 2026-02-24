<?php
declare(strict_types=1);

require_once ROOT_PATH . '/models/EvaluationModel.php';
require_once ROOT_PATH . '/models/JudgeModel.php';

class EvaluationsController extends BaseController
{
    private EvaluationModel $evaluationModel;
    private JudgeModel      $judgeModel;

    public function __construct()
    {
        parent::__construct();
        $this->evaluationModel = new EvaluationModel();
        $this->judgeModel      = new JudgeModel();
    }

    public function index(?int $id): void
    {
        $currentPage = $this->currentPage();
        $evaluations = $this->evaluationModel->findAll($currentPage);
        $pagination  = $this->buildPagination($this->evaluationModel->count(), $currentPage);

        $this->render('evaluations/index', compact('evaluations', 'pagination'));
    }

    public function show(?int $id): void
    {
        $this->requireId($id, $this->url('evaluations'));

        $evaluation = $this->evaluationModel->findById($id);
        if (!$evaluation) {
            $this->setFlash('error', 'Evaluación no encontrada.');
            $this->redirect($this->url('evaluations'));
        }

        $this->render('evaluations/show', compact('evaluation'));
    }

    public function create(?int $id): void
    {
        $projects = $this->evaluationModel->getProjectsForSelect();
        $judges   = $this->judgeModel->findAllBasic();
        // Preseleccionar proyecto si viene por GET
        $preselectedProjectId = isset($_GET['project_id']) ? (int) $_GET['project_id'] : null;

        $this->render('evaluations/create', compact('projects', 'judges', 'preselectedProjectId'));
    }

    public function store(?int $id): void
    {
        $this->requirePost($this->url('evaluations', 'create'));
        $this->validateCsrf($this->url('evaluations', 'create'));

        $data = [
            'projectId'        => $this->postInt('project_id'),
            'judgeId'          => $this->postInt('judge_id'),
            'innovationScore'   => $this->postFloat('innovation_score'),
            'technicalScore'    => $this->postFloat('technical_score'),
            'presentationScore' => $this->postFloat('presentation_score'),
            'usabilityScore'    => $this->postFloat('usability_score'),
            'comments'          => $this->post('comments'),
        ];

        $errors = $this->evaluationModel->validate($data);
        if ($errors) {
            $this->setFlash('error', implode('<br>', $errors));
            $this->redirect($this->url('evaluations', 'create'));
        }

        try {
            $newId = $this->evaluationModel->create($data);
            $this->setFlash('success', 'Evaluación registrada correctamente.');
            $this->redirect($this->url('evaluations', 'show', $newId));
        } catch (\PDOException) {
            $this->setFlash('error', 'Error al guardar la evaluación.');
            $this->redirect($this->url('evaluations', 'create'));
        }
    }

    public function delete(?int $id): void
    {
        $this->requireId($id, $this->url('evaluations'));
        $this->requirePost($this->url('evaluations'));
        $this->validateCsrf($this->url('evaluations'));

        try {
            $this->evaluationModel->delete($id);
            $this->setFlash('success', 'Evaluación eliminada.');
        } catch (\PDOException) {
            $this->setFlash('error', 'Error al eliminar la evaluación.');
        }

        $this->redirect($this->url('evaluations'));
    }
}
