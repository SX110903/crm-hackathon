<?php
declare(strict_types=1);

require_once ROOT_PATH . '/models/ParticipantModel.php';

class ParticipantsController extends BaseController
{
    private ParticipantModel $participantModel;

    public function __construct()
    {
        parent::__construct();
        $this->participantModel = new ParticipantModel();
    }

    // ─── GET: Listado ────────────────────────────────────────────────────────────
    public function index(?int $id): void
    {
        $currentPage  = $this->currentPage();
        $participants = $this->participantModel->findAll($currentPage);
        $pagination   = $this->buildPagination($this->participantModel->count(), $currentPage);

        $this->render('participants/index', compact('participants', 'pagination'));
    }

    // ─── GET: Detalle ────────────────────────────────────────────────────────────
    public function show(?int $id): void
    {
        $this->requireId($id, $this->url('participants'));

        $participant = $this->participantModel->findById($id);
        if (!$participant) {
            $this->setFlash('error', 'Participante no encontrado.');
            $this->redirect($this->url('participants'));
        }

        $this->render('participants/show', compact('participant'));
    }

    // ─── GET: Formulario de creación ─────────────────────────────────────────────
    public function create(?int $id): void
    {
        $this->render('participants/create');
    }

    // ─── POST: Guardar participante ──────────────────────────────────────────────
    public function store(?int $id): void
    {
        $this->requirePost($this->url('participants', 'create'));
        $this->validateCsrf($this->url('participants', 'create'));

        $data = [
            'firstName'   => $this->post('first_name'),
            'lastName'    => $this->post('last_name'),
            'email'       => $this->post('email'),
            'phone'       => $this->post('phone'),
            'university'  => $this->post('university'),
            'major'       => $this->post('major'),
            'yearOfStudy' => $this->postInt('year_of_study', 1),
        ];

        $errors = $this->participantModel->validate($data);
        if ($errors) {
            $this->setFlash('error', implode('<br>', $errors));
            $this->redirect($this->url('participants', 'create'));
        }

        try {
            $newId = $this->participantModel->create($data);
            $this->setFlash('success', 'Participante registrado correctamente.');
            $this->redirect($this->url('participants', 'show', $newId));
        } catch (\PDOException) {
            $this->setFlash('error', 'Error al guardar el participante. El email puede estar duplicado.');
            $this->redirect($this->url('participants', 'create'));
        }
    }

    // ─── GET: Formulario de edición ──────────────────────────────────────────────
    public function edit(?int $id): void
    {
        $this->requireId($id, $this->url('participants'));

        $participant = $this->participantModel->findById($id);
        if (!$participant) {
            $this->setFlash('error', 'Participante no encontrado.');
            $this->redirect($this->url('participants'));
        }

        $this->render('participants/edit', compact('participant'));
    }

    // ─── POST+PUT: Actualizar participante ───────────────────────────────────────
    public function update(?int $id): void
    {
        $this->requireId($id, $this->url('participants'));
        $this->requirePut($this->url('participants', 'edit', $id));
        $this->validateCsrf($this->url('participants', 'edit', $id));

        $data = [
            'firstName'   => $this->post('first_name'),
            'lastName'    => $this->post('last_name'),
            'email'       => $this->post('email'),
            'phone'       => $this->post('phone'),
            'university'  => $this->post('university'),
            'major'       => $this->post('major'),
            'yearOfStudy' => $this->postInt('year_of_study', 1),
        ];

        $errors = $this->participantModel->validate($data);
        if ($errors) {
            $this->setFlash('error', implode('<br>', $errors));
            $this->redirect($this->url('participants', 'edit', $id));
        }

        try {
            $this->participantModel->update($id, $data);
            $this->setFlash('success', 'Participante actualizado correctamente.');
            $this->redirect($this->url('participants', 'show', $id));
        } catch (\PDOException) {
            $this->setFlash('error', 'Error al actualizar. El email puede estar en uso.');
            $this->redirect($this->url('participants', 'edit', $id));
        }
    }

    // ─── POST: Eliminar participante ─────────────────────────────────────────────
    public function delete(?int $id): void
    {
        $this->requireId($id, $this->url('participants'));
        $this->requirePost($this->url('participants'));
        $this->validateCsrf($this->url('participants'));

        try {
            $this->participantModel->delete($id);
            $this->setFlash('success', 'Participante eliminado.');
        } catch (\PDOException) {
            $this->setFlash('error', 'No se puede eliminar (puede ser líder de un equipo).');
        }

        $this->redirect($this->url('participants'));
    }
}
