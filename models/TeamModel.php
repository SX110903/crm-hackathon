<?php
declare(strict_types=1);

class TeamModel extends BaseModel
{
    protected string $table      = 'Teams';
    protected string $primaryKey = 'TeamID';

    // ─── Listado con información enriquecida ─────────────────────────────────────
    public function findAll(int $page = 1): array
    {
        $offset = ($page - 1) * RECORDS_PER_PAGE;
        return $this->db->fetchAll(
            'SELECT t.TeamID, t.TeamName, t.MaxMembers, t.CreatedDate,
                    CONCAT(hp.FirstName, " ", hp.LastName) AS leaderName,
                    ts.TotalMembers,
                    COALESCE(ts.AverageProjectScore, 0)    AS avgScore,
                    (SELECT COUNT(*) FROM Projects WHERE TeamID = t.TeamID) AS projectCount
             FROM Teams t
             LEFT JOIN HackathonParticipants hp ON t.TeamLeaderID = hp.ParticipantID
             LEFT JOIN TeamStatistics ts ON t.TeamID = ts.TeamID
             ORDER BY t.TeamID DESC
             LIMIT ? OFFSET ?',
            [RECORDS_PER_PAGE, $offset]
        );
    }

    // ─── Detalle de equipo con líder ─────────────────────────────────────────────
    public function findById(int $id): array|false
    {
        return $this->db->fetchOne(
            'SELECT t.*,
                    CONCAT(hp.FirstName, " ", hp.LastName) AS leaderName,
                    hp.Email AS leaderEmail,
                    ts.TotalMembers, ts.TotalMentoringHours,
                    ts.TotalMentoringSessions,
                    COALESCE(ts.AverageProjectScore, 0)    AS avgScore
             FROM Teams t
             LEFT JOIN HackathonParticipants hp ON t.TeamLeaderID = hp.ParticipantID
             LEFT JOIN TeamStatistics ts ON t.TeamID = ts.TeamID
             WHERE t.TeamID = ?',
            [$id]
        );
    }

    // ─── Miembros del equipo ─────────────────────────────────────────────────────
    public function getMembers(int $teamId): array
    {
        return $this->db->fetchAll(
            'SELECT tm.MemberID, tm.Role, tm.JoinedDate,
                    hp.ParticipantID,
                    CONCAT(hp.FirstName, " ", hp.LastName) AS memberName,
                    hp.Email, hp.University, hp.Major
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
            'SELECT p.*, ROUND(AVG(e.TotalScore), 2) AS avgScore
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
            'SELECT ms.SessionID, ms.SessionDate, ms.Duration, ms.Topic, ms.Notes,
                    CONCAT(m.FirstName, " ", m.LastName) AS mentorName,
                    m.Specialization
             FROM MentoringSessions ms
             JOIN Mentors m ON ms.MentorID = m.MentorID
             WHERE ms.TeamID = ?
             ORDER BY ms.SessionDate DESC',
            [$teamId]
        );
    }

    // ─── Participantes sin equipo (para el selector) ─────────────────────────────
    public function getAvailableLeaders(): array
    {
        return $this->db->fetchAll(
            'SELECT ParticipantID,
                    CONCAT(FirstName, " ", LastName) AS fullName, Email
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

            // El líder es también miembro del equipo
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

    // ─── Añadir/Quitar miembros ──────────────────────────────────────────────────

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
