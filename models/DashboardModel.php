<?php
declare(strict_types=1);

/**
 * DashboardModel — Consultas agregadas para el panel de control.
 * No hereda BaseModel porque no mapea a una tabla única.
 */
class DashboardModel
{
    private Database $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    // ─── Tarjetas de estadísticas ────────────────────────────────────────────────
    public function getStats(): array
    {
        return [
            'participants'    => (int) $this->db->fetchScalar('SELECT COUNT(*) FROM HackathonParticipants'),
            'teams'           => (int) $this->db->fetchScalar('SELECT COUNT(*) FROM Teams'),
            'projects'        => (int) $this->db->fetchScalar('SELECT COUNT(*) FROM Projects'),
            'evaluations'     => (int) $this->db->fetchScalar('SELECT COUNT(*) FROM Evaluations'),
            'mentors'         => (int) $this->db->fetchScalar('SELECT COUNT(*) FROM Mentors'),
            'judges'          => (int) $this->db->fetchScalar('SELECT COUNT(*) FROM Judges'),
            'mentoringSessions' => (int) $this->db->fetchScalar('SELECT COUNT(*) FROM MentoringSessions'),
            'awards'          => (int) $this->db->fetchScalar('SELECT COUNT(*) FROM Awards'),
            'awardsAssigned'  => (int) $this->db->fetchScalar('SELECT COUNT(*) FROM Awards WHERE ProjectID IS NOT NULL'),
        ];
    }

    // ─── Ranking de proyectos ────────────────────────────────────────────────────
    public function getProjectRankings(int $limit = 5): array
    {
        return $this->db->fetchAll(
            'SELECT p.ProjectID, p.ProjectName, t.TeamName, p.Category, p.Status,
                    ROUND(AVG(e.TotalScore), 2)       AS avgScore,
                    COUNT(e.EvaluationID)              AS totalEvaluations
             FROM Projects p
             JOIN Teams t ON p.TeamID = t.TeamID
             LEFT JOIN Evaluations e ON p.ProjectID = e.ProjectID
             GROUP BY p.ProjectID, p.ProjectName, t.TeamName, p.Category, p.Status
             ORDER BY avgScore DESC
             LIMIT ?',
            [$limit]
        );
    }

    // ─── Eventos recientes ───────────────────────────────────────────────────────
    public function getRecentEvents(int $limit = 10): array
    {
        return $this->db->fetchAll(
            'SELECT el.EventID, el.EventType, el.Description,
                    t.TeamName, p.ProjectName, el.EventDate
             FROM EventLog el
             LEFT JOIN Teams    t ON el.RelatedTeamID    = t.TeamID
             LEFT JOIN Projects p ON el.RelatedProjectID = p.ProjectID
             ORDER BY el.EventDate DESC
             LIMIT ?',
            [$limit]
        );
    }

    // ─── Distribución de proyectos por estado ────────────────────────────────────
    public function getProjectStatusDistribution(): array
    {
        return $this->db->fetchAll(
            'SELECT Status, COUNT(*) AS total
             FROM Projects
             GROUP BY Status
             ORDER BY total DESC'
        );
    }

    // ─── Equipos con mayor puntuación ───────────────────────────────────────────
    public function getTopTeams(int $limit = 5): array
    {
        return $this->db->fetchAll(
            'SELECT t.TeamID, t.TeamName,
                    CONCAT(hp.FirstName, " ", hp.LastName) AS leaderName,
                    ts.TotalMembers,
                    COALESCE(ts.AverageProjectScore, 0) AS avgScore
             FROM Teams t
             LEFT JOIN HackathonParticipants hp ON t.TeamLeaderID = hp.ParticipantID
             LEFT JOIN TeamStatistics ts ON t.TeamID = ts.TeamID
             ORDER BY avgScore DESC
             LIMIT ?',
            [$limit]
        );
    }
}
