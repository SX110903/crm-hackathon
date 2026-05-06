<?php
declare(strict_types=1);

class ProjectModel extends BaseModel
{
    protected string $table      = 'Projects';
    protected string $primaryKey = 'ProjectID';

    // ─── Listado con puntuación media ────────────────────────────────────────────
    public function findAll(int $page = 1): array
    {
        $offset = ($page - 1) * RECORDS_PER_PAGE;
        return $this->db->fetchAll(
            'SELECT p.ProjectID, p.ProjectName, p.Category, p.Status,
                    p.SubmissionDate, p.GitHubURL,
                    t.TeamName,
                    ROUND(AVG(e.TotalScore), 2)  AS avgScore,
                    COUNT(e.EvaluationID)         AS totalEvaluations
             FROM Projects p
             JOIN Teams t ON p.TeamID = t.TeamID
             LEFT JOIN Evaluations e ON p.ProjectID = e.ProjectID
             GROUP BY p.ProjectID, p.ProjectName, p.Category, p.Status,
                      p.SubmissionDate, p.GitHubURL, t.TeamName
             ORDER BY p.ProjectID DESC
             LIMIT ? OFFSET ?',
            [RECORDS_PER_PAGE, $offset]
        );
    }

    // ─── Detalle completo ────────────────────────────────────────────────────────
    public function findById(int $id): array|false
    {
        return $this->db->fetchOne(
            'SELECT p.*,
                    t.TeamName,
                    CONCAT(hp.FirstName, " ", hp.LastName) AS leaderName,
                    ROUND(AVG(e.TotalScore), 2)            AS avgScore,
                    COUNT(e.EvaluationID)                  AS totalEvaluations
             FROM Projects p
             JOIN Teams t ON p.TeamID = t.TeamID
             LEFT JOIN HackathonParticipants hp ON t.TeamLeaderID = hp.ParticipantID
             LEFT JOIN Evaluations e ON p.ProjectID = e.ProjectID
             WHERE p.ProjectID = ?
             GROUP BY p.ProjectID',
            [$id]
        );
    }

    // ─── Evaluaciones de un proyecto ─────────────────────────────────────────────
    public function getEvaluations(int $projectId): array
    {
        return $this->db->fetchAll(
            'SELECT e.*,
                    CONCAT(j.FirstName, " ", j.LastName) AS judgeName,
                    j.Company, j.Expertise
             FROM Evaluations e
             JOIN Judges j ON e.JudgeID = j.JudgeID
             WHERE e.ProjectID = ?
             ORDER BY e.EvaluationDate DESC',
            [$projectId]
        );
    }

    // ─── Rankings completos ───────────────────────────────────────────────────────
    public function getRankings(): array
    {
        return $this->db->fetchAll(
            'SELECT p.ProjectID, p.ProjectName, t.TeamName, p.Category, p.Status,
                    ROUND(AVG(e.TotalScore), 2)        AS avgScore,
                    COUNT(e.EvaluationID)               AS totalEvaluations,
                    ROUND(AVG(e.InnovationScore), 2)    AS avgInnovation,
                    ROUND(AVG(e.TechnicalScore), 2)     AS avgTechnical,
                    ROUND(AVG(e.PresentationScore), 2)  AS avgPresentation,
                    ROUND(AVG(e.UsabilityScore), 2)     AS avgUsability
             FROM Projects p
             JOIN Teams t ON p.TeamID = t.TeamID
             LEFT JOIN Evaluations e ON p.ProjectID = e.ProjectID
             GROUP BY p.ProjectID, p.ProjectName, t.TeamName, p.Category, p.Status
             ORDER BY avgScore DESC'
        );
    }

    // ─── Lista de equipos para selector ──────────────────────────────────────────
    public function getTeamsForSelect(): array
    {
        return $this->db->fetchAll(
            'SELECT t.TeamID, t.TeamName,
                    (SELECT COUNT(*) FROM Projects WHERE TeamID = t.TeamID) AS projectCount
             FROM Teams t
             ORDER BY t.TeamName'
        );
    }

    // ─── CRUD ───────────────────────────────────────────────────────────────────

    public function create(array $cleanData): int
    {
        $this->db->execute(
            'INSERT INTO Projects
                (TeamID, ProjectName, Description, Category, TechnologyStack, GitHubURL, Status)
             VALUES (?, ?, ?, ?, ?, ?, ?)',
            [
                $cleanData['teamId'],
                $cleanData['projectName'],
                $cleanData['description'],
                $cleanData['category'],
                $cleanData['technologyStack'],
                $cleanData['githubUrl'],
                $cleanData['status'],
            ]
        );
        return $this->db->lastInsertId();
    }

    public function update(int $id, array $cleanData): int
    {
        return $this->db->execute(
            'UPDATE Projects
             SET ProjectName     = ?,
                 Description     = ?,
                 Category        = ?,
                 TechnologyStack = ?,
                 GitHubURL       = ?,
                 Status          = ?
             WHERE ProjectID = ?',
            [
                $cleanData['projectName'],
                $cleanData['description'],
                $cleanData['category'],
                $cleanData['technologyStack'],
                $cleanData['githubUrl'],
                $cleanData['status'],
                $id,
            ]
        );
    }

    // ─── Validación ─────────────────────────────────────────────────────────────

    public function validate(array $rawData): array
    {
        return $this->mergeErrors(
            $this->requireFields([
                'teamId'      => 'Equipo',
                'projectName' => 'Nombre del proyecto',
                'category'    => 'Categoría',
                'status'      => 'Estado',
            ], $rawData),
            $this->requireInList('category', 'Categoría', PROJECT_CATEGORIES, $rawData),
            $this->requireInList('status',   'Estado',    PROJECT_STATUSES,   $rawData),
            $this->requireMaxLength('projectName', 'Nombre del proyecto', 200, $rawData)
        );
    }
}
