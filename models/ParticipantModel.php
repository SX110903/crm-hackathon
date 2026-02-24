<?php
declare(strict_types=1);

class ParticipantModel extends BaseModel
{
    protected string $table      = 'HackathonParticipants';
    protected string $primaryKey = 'ParticipantID';

    // ─── Listado con equipo asociado ─────────────────────────────────────────────
    public function findAll(int $page = 1): array
    {
        $offset = ($page - 1) * RECORDS_PER_PAGE;
        return $this->db->fetchAll(
            'SELECT hp.ParticipantID, hp.FirstName, hp.LastName, hp.Email,
                    hp.University, hp.Major, hp.YearOfStudy, hp.RegistrationDate,
                    t.TeamName,
                    tm.Role AS teamRole
             FROM HackathonParticipants hp
             LEFT JOIN TeamMembers tm ON hp.ParticipantID = tm.ParticipantID
             LEFT JOIN Teams t ON tm.TeamID = t.TeamID
             ORDER BY hp.ParticipantID DESC
             LIMIT ? OFFSET ?',
            [RECORDS_PER_PAGE, $offset]
        );
    }

    // ─── Detalle completo ────────────────────────────────────────────────────────
    public function findById(int $id): array|false
    {
        return $this->db->fetchOne(
            'SELECT hp.*,
                    t.TeamID, t.TeamName,
                    tm.Role AS teamRole, tm.JoinedDate
             FROM HackathonParticipants hp
             LEFT JOIN TeamMembers tm ON hp.ParticipantID = tm.ParticipantID
             LEFT JOIN Teams t ON tm.TeamID = t.TeamID
             WHERE hp.ParticipantID = ?',
            [$id]
        );
    }

    /** Lista básica para selectores (id + nombre). */
    public function findAllBasic(): array
    {
        return $this->db->fetchAll(
            'SELECT ParticipantID,
                    CONCAT(FirstName, " ", LastName) AS fullName, Email
             FROM HackathonParticipants
             ORDER BY FirstName, LastName'
        );
    }

    /** Participantes que no pertenecen a ningún equipo todavía. */
    public function findWithoutTeam(): array
    {
        return $this->db->fetchAll(
            'SELECT hp.ParticipantID,
                    CONCAT(hp.FirstName, " ", hp.LastName) AS fullName,
                    hp.Email
             FROM HackathonParticipants hp
             WHERE hp.ParticipantID NOT IN (SELECT ParticipantID FROM TeamMembers)
             ORDER BY hp.FirstName, hp.LastName'
        );
    }

    // ─── CRUD ───────────────────────────────────────────────────────────────────

    public function create(array $cleanData): int
    {
        $this->db->execute(
            'INSERT INTO HackathonParticipants
                (FirstName, LastName, Email, Phone, University, Major, YearOfStudy)
             VALUES (?, ?, ?, ?, ?, ?, ?)',
            [
                $cleanData['firstName'],
                $cleanData['lastName'],
                $cleanData['email'],
                $cleanData['phone'],
                $cleanData['university'],
                $cleanData['major'],
                $cleanData['yearOfStudy'],
            ]
        );
        return $this->db->lastInsertId();
    }

    public function update(int $id, array $cleanData): int
    {
        return $this->db->execute(
            'UPDATE HackathonParticipants
             SET FirstName   = ?,
                 LastName    = ?,
                 Email       = ?,
                 Phone       = ?,
                 University  = ?,
                 Major       = ?,
                 YearOfStudy = ?
             WHERE ParticipantID = ?',
            [
                $cleanData['firstName'],
                $cleanData['lastName'],
                $cleanData['email'],
                $cleanData['phone'],
                $cleanData['university'],
                $cleanData['major'],
                $cleanData['yearOfStudy'],
                $id,
            ]
        );
    }

    // ─── Validación ─────────────────────────────────────────────────────────────

    public function validate(array $rawData): array
    {
        return $this->mergeErrors(
            $this->requireFields([
                'firstName'  => 'Nombre',
                'lastName'   => 'Apellidos',
                'email'      => 'Email',
                'university' => 'Universidad',
                'major'      => 'Carrera',
            ], $rawData),
            $this->requireValidEmail('email', 'Email', $rawData),
            $this->requireNumericRange('yearOfStudy', 'Año de estudio', 1, 6, $rawData),
            $this->requireMaxLength('firstName',  'Nombre',       100, $rawData),
            $this->requireMaxLength('lastName',   'Apellidos',    100, $rawData),
            $this->requireMaxLength('email',      'Email',        150, $rawData),
            $this->requireMaxLength('university', 'Universidad',  200, $rawData)
        );
    }
}
