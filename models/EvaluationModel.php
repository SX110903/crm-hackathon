<?php
declare(strict_types=1);

class EvaluationModel extends BaseModel
{
    protected string $table      = 'Evaluations';
    protected string $primaryKey = 'EvaluationID';

    // ─── Listado con proyecto y juez ─────────────────────────────────────────────
    public function findAll(int $page = 1): array
    {
        $offset = ($page - 1) * RECORDS_PER_PAGE;
        return $this->db->fetchAll(
            'SELECT e.EvaluationID, e.TotalScore, e.EvaluationDate,
                    e.InnovationScore, e.TechnicalScore,
                    e.PresentationScore, e.UsabilityScore,
                    p.ProjectName,
                    CONCAT(j.FirstName, " ", j.LastName) AS judgeName
             FROM Evaluations e
             JOIN Projects p ON e.ProjectID = p.ProjectID
             JOIN Judges   j ON e.JudgeID   = j.JudgeID
             ORDER BY e.EvaluationDate DESC
             LIMIT ? OFFSET ?',
            [RECORDS_PER_PAGE, $offset]
        );
    }

    // ─── Detalle completo ────────────────────────────────────────────────────────
    public function findById(int $id): array|false
    {
        return $this->db->fetchOne(
            'SELECT e.*,
                    p.ProjectName, t.TeamName,
                    CONCAT(j.FirstName, " ", j.LastName) AS judgeName,
                    j.Company, j.Expertise
             FROM Evaluations e
             JOIN Projects p ON e.ProjectID = p.ProjectID
             JOIN Teams    t ON p.TeamID    = t.TeamID
             JOIN Judges   j ON e.JudgeID   = j.JudgeID
             WHERE e.EvaluationID = ?',
            [$id]
        );
    }

    /** Proyectos disponibles para evaluar (con el equipo). */
    public function getProjectsForSelect(): array
    {
        return $this->db->fetchAll(
            'SELECT p.ProjectID,
                    CONCAT(p.ProjectName, " — ", t.TeamName) AS displayName,
                    p.Status
             FROM Projects p
             JOIN Teams t ON p.TeamID = t.TeamID
             WHERE p.Status IN ("Submitted","Under Review")
             ORDER BY p.ProjectName'
        );
    }

    // ─── CRUD ───────────────────────────────────────────────────────────────────

    public function create(array $cleanData): int
    {
        $this->db->execute(
            'INSERT INTO Evaluations
                (ProjectID, JudgeID, InnovationScore, TechnicalScore,
                 PresentationScore, UsabilityScore, Comments)
             VALUES (?, ?, ?, ?, ?, ?, ?)',
            [
                $cleanData['projectId'],
                $cleanData['judgeId'],
                $cleanData['innovationScore'],
                $cleanData['technicalScore'],
                $cleanData['presentationScore'],
                $cleanData['usabilityScore'],
                $cleanData['comments'],
            ]
        );
        return $this->db->lastInsertId();
    }

    public function update(int $id, array $cleanData): int
    {
        return $this->db->execute(
            'UPDATE Evaluations
             SET InnovationScore    = ?,
                 TechnicalScore     = ?,
                 PresentationScore  = ?,
                 UsabilityScore     = ?,
                 Comments           = ?
             WHERE EvaluationID = ?',
            [
                $cleanData['innovationScore'],
                $cleanData['technicalScore'],
                $cleanData['presentationScore'],
                $cleanData['usabilityScore'],
                $cleanData['comments'],
                $id,
            ]
        );
    }

    // ─── Validación ─────────────────────────────────────────────────────────────

    public function validate(array $rawData): array
    {
        return $this->mergeErrors(
            $this->requireFields([
                'projectId' => 'Proyecto',
                'judgeId'   => 'Juez',
            ], $rawData),
            $this->requireNumericRange('innovationScore',    'Innovación',     0, 10, $rawData),
            $this->requireNumericRange('technicalScore',     'Técnica',        0, 10, $rawData),
            $this->requireNumericRange('presentationScore',  'Presentación',   0, 10, $rawData),
            $this->requireNumericRange('usabilityScore',     'Usabilidad',     0, 10, $rawData)
        );
    }
}
