<?php
declare(strict_types=1);

require_once ROOT_PATH . '/models/MentorModel.php';
//Aqui aplico todos los terminos de la POO, aplicado a las clases realmente esto es una interfaz para poder ser usado en otras partes del codigo
//E
class MentorsController extends BaseController
{
    private MentorModel $mentorModel;

    public function __construct()
    {
        parent::__construct();
        $this->mentorModel = new MentorModel();
    }

    public function index(?int $id): void
    {
        $currentPage = $this->currentPage();
        $mentors     = $this->mentorModel->findAll($currentPage);
        $pagination  = $this->buildPagination($this->mentorModel->count(), $currentPage);

        $this->render('mentors/index', compact('mentors', 'pagination'));
    }

    public function show(?int $id): void
    {
        $this->requireId($id, $this->url('mentors'));

        $mentor   = $this->mentorModel->findById($id);
        if (!$mentor) {
            $this->setFlash('error', 'Mentor no encontrado.');
            $this->redirect($this->url('mentors'));
        }

        $sessions = $this->mentorModel->getSessions($id);

        $this->render('mentors/show', compact('mentor', 'sessions'));
    }

    public function create(?int $id): void
    {
        $this->render('mentors/create');
    }

    public function store(?int $id): void
    {
        $this->requirePost($this->url('mentors', 'create'));
        $this->validateCsrf($this->url('mentors', 'create'));

        $data = [
            'firstName'      => $this->post('first_name'),
            'lastName'       => $this->post('last_name'),
            'email'          => $this->post('email'),
            'company'        => $this->post('company'),
            'specialization' => $this->post('specialization'),
            'availableSlots' => $this->postInt('available_slots', 0),
        ];

        $errors = $this->mentorModel->validate($data);
        if ($errors) {
            $this->setFlash('error', implode('<br>', $errors));
            $this->redirect($this->url('mentors', 'create'));
        }

        try {
            $newId = $this->mentorModel->create($data);
            $this->setFlash('success', 'Mentor registrado correctamente.');
            $this->redirect($this->url('mentors', 'show', $newId));
        } catch (\PDOException) {
            $this->setFlash('error', 'Error al guardar el mentor. El email puede estar duplicado.');
            $this->redirect($this->url('mentors', 'create'));
        }
    }

    public function edit(?int $id): void
    {
        $this->requireId($id, $this->url('mentors'));

        $mentor = $this->mentorModel->findById($id);
        if (!$mentor) {
            $this->setFlash('error', 'Mentor no encontrado.');
            $this->redirect($this->url('mentors'));
        }

        $this->render('mentors/edit', compact('mentor'));
    }

    public function update(?int $id): void
    {
        $this->requireId($id, $this->url('mentors'));
        $this->requirePut($this->url('mentors', 'edit', $id));
        $this->validateCsrf($this->url('mentors', 'edit', $id));

        $data = [
            'firstName'      => $this->post('first_name'),
            'lastName'       => $this->post('last_name'),
            'email'          => $this->post('email'),
            'company'        => $this->post('company'),
            'specialization' => $this->post('specialization'),
            'availableSlots' => $this->postInt('available_slots', 0),
        ];

        $errors = $this->mentorModel->validate($data);
        if ($errors) {
            $this->setFlash('error', implode('<br>', $errors));
            $this->redirect($this->url('mentors', 'edit', $id));
        }

        try {
            $this->mentorModel->update($id, $data);
            $this->setFlash('success', 'Mentor actualizado correctamente.');
            $this->redirect($this->url('mentors', 'show', $id));
        } catch (\PDOException) {
            $this->setFlash('error', 'Error al actualizar el mentor.');
            $this->redirect($this->url('mentors', 'edit', $id));
        }
    }

    public function delete(?int $id): void
    {
        $this->requireId($id, $this->url('mentors'));
        $this->requirePost($this->url('mentors'));
        $this->validateCsrf($this->url('mentors'));

        try {
            $this->mentorModel->delete($id);
            $this->setFlash('success', 'Mentor eliminado.');
        } catch (\PDOException) {
            $this->setFlash('error', 'No se puede eliminar (tiene sesiones registradas).');
        }

        $this->redirect($this->url('mentors'));
    }
}
