<?php
declare(strict_types=1);

/**
 * TeamModel — Gestiona los datos de equipos del hackathon.
 *
 * Las queries usan subqueries directas en lugar de la vista TeamStatistics
 * para evitar dependencia de un objeto de BD opcional.
 */
class TeamModel extends BaseModel
{
    protected string $table      = 'Teams';
    protected string $primaryKey = 'TeamID';

    // ─── Listado con información enriquecida ─────────────────────────────────────
    public function findAll(int $page = 1): array
    {
        $offset = ($page - 1) * RECORDS_PER_PAGE;
        return $this->db->fetchAll(
            'SELECT t.TeamID,
                    t.TeamName,
                    t.MaxMembers,
                    t.CreatedDate,
                    CONCAT(hp.FirstName, " ", hp.LastName) AS leaderName,
                    COUNT(DISTINCT tm.MemberID)             AS TotalMembers,
                    COUNT(DISTINCT p.ProjectID)             AS projectCount,
                    COALESCE(ROUND(AVG(e.TotalScore), 1), 0) AS avgScore
             FROM Teams t
             LEFT JOIN HackathonParticipants hp ON t.TeamLeaderID = hp.ParticipantID
             LEFT JOIN TeamMembers tm            ON t.TeamID = tm.TeamID
             LEFT JOIN Projects p               ON t.TeamID = p.TeamID
             LEFT JOIN Evaluations e            ON p.ProjectID = e.ProjectID
             GROUP BY t.TeamID, t.TeamName, t.MaxMembers, t.CreatedDate,
                      hp.FirstName, hp.LastName
             ORDER BY t.TeamID DESC
             LIMIT ? OFFSET ?',
            [RECORDS_PER_PAGE, $offset]
        );
    }

    /**
     * Búsqueda de equipos por nombre (parcial, case-insensitive).
     * Devuelve resultados paginados.
     */
    public function search(string $query, int $page = 1): array
    {
        $offset = ($page - 1) * RECORDS_PER_PAGE;
        $like   = '%' . $query . '%';
        return $this->db->fetchAll(
            'SELECT t.TeamID,
                    t.TeamName,
                    t.MaxMembers,
                    t.CreatedDate,
                    CONCAT(hp.FirstName, " ", hp.LastName) AS leaderName,
                    COUNT(DISTINCT tm.MemberID)             AS TotalMembers,
                    COUNT(DISTINCT p.ProjectID)             AS projectCount,
                    COALESCE(ROUND(AVG(e.TotalScore), 1), 0) AS avgScore
             FROM Teams t
             LEFT JOIN HackathonParticipants hp ON t.TeamLeaderID = hp.ParticipantID
             LEFT JOIN TeamMembers tm            ON t.TeamID = tm.TeamID
             LEFT JOIN Projects p               ON t.TeamID = p.TeamID
             LEFT JOIN Evaluations e            ON p.ProjectID = e.ProjectID
             WHERE t.TeamName LIKE ?
                OR CONCAT(hp.FirstName, " ", hp.LastName) LIKE ?
             GROUP BY t.TeamID, t.TeamName, t.MaxMembers, t.CreatedDate,
                      hp.FirstName, hp.LastName
             ORDER BY t.TeamID DESC
             LIMIT ? OFFSET ?',
            [$like, $like, RECORDS_PER_PAGE, $offset]
        );
    }

    /** Total de equipos que coinciden con la búsqueda (para paginación). */
    public function countSearch(string $query): int
    {
        $like = '%' . $query . '%';
        return (int) $this->db->fetchScalar(
            'SELECT COUNT(DISTINCT t.TeamID)
             FROM Teams t
             LEFT JOIN HackathonParticipants hp ON t.TeamLeaderID = hp.ParticipantID
             WHERE t.TeamName LIKE ?
                OR CONCAT(hp.FirstName, " ", hp.LastName) LIKE ?',
            [$like, $like]
        );
    }

    // ─── Detalle de equipo con estadísticas (sin dependencia de vista SQL) ────────
    public function findById(int $id): array|false
    {
        return $this->db->fetchOne(
            'SELECT t.*,
                    CONCAT(hp.FirstName, " ", hp.LastName) AS leaderName,
                    hp.Email                               AS leaderEmail,
                    COUNT(DISTINCT tm.MemberID)            AS TotalMembers,
                    COALESCE(
                        ROUND(SUM(DISTINCT ms.Duration) / 60, 1), 0
                    )                                      AS TotalMentoringHours,
                    COUNT(DISTINCT ms.SessionID)           AS TotalMentoringSessions,
                    COALESCE(ROUND(AVG(e.TotalScore), 2), 0) AS avgScore
             FROM Teams t
             LEFT JOIN HackathonParticipants hp ON t.TeamLeaderID = hp.ParticipantID
             LEFT JOIN TeamMembers tm            ON t.TeamID = tm.TeamID
             LEFT JOIN Projects p               ON t.TeamID = p.TeamID
             LEFT JOIN Evaluations e            ON p.ProjectID = e.ProjectID
             LEFT JOIN MentoringSessions ms     ON t.TeamID = ms.TeamID
             WHERE t.TeamID = ?
             GROUP BY t.TeamID, t.TeamName, t.MaxMembers, t.TeamLeaderID,
                      t.CreatedDate, hp.FirstName, hp.LastName, hp.Email',
            [$id]
        );
    }

    // ─── Miembros del equipo ─────────────────────────────────────────────────────
    public function getMembers(int $teamId): array
    {
        return $this->db->fetchAll(
            'SELECT tm.MemberID,
                    tm.Role,
                    tm.JoinedDate,
                    hp.ParticipantID,
                    CONCAT(hp.FirstName, " ", hp.LastName) AS memberName,
                    hp.Email,
                    hp.University,
                    hp.Major
             FROM TeamMembers tm
             JOIN HackathonParticipants hp ON tm.ParticipantID = hp.ParticipantID
             WHERE tm.TeamID = ?
             ORDER BY tm.JoinedDate',
            [$teamId]
        );
    }

    // ─── Proyecto del equipo ─────────────────────────────────────────────────────
    public function getProject(int $teamId): array|false
    {
        return $this->db->fetchOne(
            'SELECT p.*,
                    ROUND(AVG(e.TotalScore), 2) AS avgScore
             FROM Projects p
             LEFT JOIN Evaluations e ON p.ProjectID = e.ProjectID
             WHERE p.TeamID = ?
             GROUP BY p.ProjectID',
            [$teamId]
        );
    }

    // ─── Sesiones de mentoría del equipo ────────────────────────────────────────
    public function getSessions(int $teamId): array
    {
        return $this->db->fetchAll(
            'SELECT ms.SessionID,
                    ms.SessionDate,
                    ms.Duration,
                    ms.Topic,
                    ms.Notes,
                    CONCAT(m.FirstName, " ", m.LastName) AS mentorName,
                    m.Specialization
             FROM MentoringSessions ms
             JOIN Mentors m ON ms.MentorID = m.MentorID
             WHERE ms.TeamID = ?
             ORDER BY ms.SessionDate DESC',
            [$teamId]
        );
    }

    // ─── Participantes disponibles (para el selector de líder) ───────────────────
    public function getAvailableLeaders(): array
    {
        return $this->db->fetchAll(
            'SELECT ParticipantID,
                    CONCAT(FirstName, " ", LastName) AS fullName,
                    Email
             FROM HackathonParticipants
             ORDER BY FirstName, LastName'
        );
    }

    // ─── CRUD ───────────────────────────────────────────────────────────────────

    public function create(array $cleanData): int
    {
        $this->db->beginTransaction();
        try {
            $this->db->execute(
                'INSERT INTO Teams (TeamName, TeamLeaderID, MaxMembers)
                 VALUES (?, ?, ?)',
                [
                    $cleanData['teamName'],
                    $cleanData['leaderId'],
                    $cleanData['maxMembers'],
                ]
            );
            $teamId = $this->db->lastInsertId();

            // El líder es también el primer miembro del equipo
            $this->db->execute(
                'INSERT INTO TeamMembers (TeamID, ParticipantID, Role)
                 VALUES (?, ?, "Leader")',
                [$teamId, $cleanData['leaderId']]
            );

            $this->db->commit();
            return $teamId;
        } catch (\PDOException $e) {
            $this->db->rollback();
            throw $e;
        }
    }

    public function update(int $id, array $cleanData): int
    {
        return $this->db->execute(
            'UPDATE Teams SET TeamName = ?, MaxMembers = ? WHERE TeamID = ?',
            [$cleanData['teamName'], $cleanData['maxMembers'], $id]
        );
    }

    // ─── Añadir / Quitar miembros ────────────────────────────────────────────────

    public function addMember(int $teamId, int $participantId, string $role): int
    {
        return $this->db->execute(
            'INSERT IGNORE INTO TeamMembers (TeamID, ParticipantID, Role)
             VALUES (?, ?, ?)',
            [$teamId, $participantId, $role]
        );
    }

    public function removeMember(int $teamId, int $participantId): int
    {
        return $this->db->execute(
            'DELETE FROM TeamMembers WHERE TeamID = ? AND ParticipantID = ?',
            [$teamId, $participantId]
        );
    }

    // ─── Validación ─────────────────────────────────────────────────────────────

    public function validate(array $rawData): array
    {
        return $this->mergeErrors(
            $this->requireFields([
                'teamName' => 'Nombre del equipo',
                'leaderId' => 'Líder del equipo',
            ], $rawData),
            $this->requireNumericRange('maxMembers', 'Máximo de miembros', 2, 20, $rawData),
            $this->requireMaxLength('teamName', 'Nombre del equipo', 100, $rawData)
        );
    }
}
