<?php
declare(strict_types=1);

class MentorModel extends BaseModel
{
    protected string $table      = 'Mentors';
    protected string $primaryKey = 'MentorID';

    // ─── Listado con totales de sesiones ────────────────────────────────────────
    public function findAll(int $page = 1): array
    {
        $offset = ($page - 1) * RECORDS_PER_PAGE;
        return $this->db->fetchAll(
            'SELECT m.MentorID, m.FirstName, m.LastName, m.Email,
                    m.Company, m.Specialization, m.AvailableSlots,
                    COUNT(ms.SessionID)        AS totalSessions,
                    COALESCE(SUM(ms.Duration), 0) AS totalMinutes
             FROM Mentors m
             LEFT JOIN MentoringSessions ms ON m.MentorID = ms.MentorID
             GROUP BY m.MentorID, m.FirstName, m.LastName, m.Email,
                      m.Company, m.Specialization, m.AvailableSlots
             ORDER BY m.MentorID DESC
             LIMIT ? OFFSET ?',
            [RECORDS_PER_PAGE, $offset]
        );
    }

    // ─── Detalle con sesiones ────────────────────────────────────────────────────
    public function getSessions(int $mentorId): array
    {
        return $this->db->fetchAll(
            'SELECT ms.SessionID, ms.SessionDate, ms.Duration, ms.Topic, ms.Notes,
                    t.TeamName
             FROM MentoringSessions ms
             JOIN Teams t ON ms.TeamID = t.TeamID
             WHERE ms.MentorID = ?
             ORDER BY ms.SessionDate DESC',
            [$mentorId]
        );
    }

    /** Lista básica para el selector de sesiones. */
    public function findAllBasic(): array
    {
        return $this->db->fetchAll(
            'SELECT MentorID,
                    CONCAT(FirstName, " ", LastName) AS fullName,
                    Specialization
             FROM Mentors
             ORDER BY FirstName, LastName'
        );
    }

    // ─── CRUD ───────────────────────────────────────────────────────────────────

    public function create(array $cleanData): int
    {
        $this->db->execute(
            'INSERT INTO Mentors (FirstName, LastName, Email, Company, Specialization, AvailableSlots)
             VALUES (?, ?, ?, ?, ?, ?)',
            [
                $cleanData['firstName'],
                $cleanData['lastName'],
                $cleanData['email'],
                $cleanData['company'],
                $cleanData['specialization'],
                $cleanData['availableSlots'],
            ]
        );
        return $this->db->lastInsertId();
    }

    public function update(int $id, array $cleanData): int
    {
        return $this->db->execute(
            'UPDATE Mentors
             SET FirstName      = ?,
                 LastName       = ?,
                 Email          = ?,
                 Company        = ?,
                 Specialization = ?,
                 AvailableSlots = ?
             WHERE MentorID = ?',
            [
                $cleanData['firstName'],
                $cleanData['lastName'],
                $cleanData['email'],
                $cleanData['company'],
                $cleanData['specialization'],
                $cleanData['availableSlots'],
                $id,
            ]
        );
    }

    // ─── Validación ─────────────────────────────────────────────────────────────

    public function validate(array $rawData): array
    {
        return $this->mergeErrors(
            $this->requireFields([
                'firstName'      => 'Nombre',
                'lastName'       => 'Apellidos',
                'email'          => 'Email',
                'specialization' => 'Especialización',
            ], $rawData),
            $this->requireValidEmail('email', 'Email', $rawData),
            $this->requireNumericRange('availableSlots', 'Slots disponibles', 0, 50, $rawData),
            $this->requireMaxLength('firstName', 'Nombre',    100, $rawData),
            $this->requireMaxLength('lastName',  'Apellidos', 100, $rawData),
            $this->requireMaxLength('email',     'Email',     150, $rawData)
        );
    }
}
