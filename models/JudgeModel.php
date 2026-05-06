<?php
declare(strict_types=1);

class JudgeModel extends BaseModel
{
    protected string $table      = 'Judges';
    protected string $primaryKey = 'JudgeID';

    // ─── Listado con conteo de evaluaciones ─────────────────────────────────────
    public function findAll(int $page = 1): array
    {
        $offset = ($page - 1) * RECORDS_PER_PAGE;
        return $this->db->fetchAll(
            'SELECT j.JudgeID, j.FirstName, j.LastName, j.Email,
                    j.Company, j.Expertise, j.YearsOfExperience,
                    COUNT(e.EvaluationID)          AS totalEvaluations,
                    ROUND(AVG(e.TotalScore), 2)    AS avgScoreGiven
             FROM Judges j
             LEFT JOIN Evaluations e ON j.JudgeID = e.JudgeID
             GROUP BY j.JudgeID, j.FirstName, j.LastName, j.Email,
                      j.Company, j.Expertise, j.YearsOfExperience
             ORDER BY j.JudgeID DESC
             LIMIT ? OFFSET ?',
            [RECORDS_PER_PAGE, $offset]
        );
    }

    /** Evaluaciones realizadas por este juez. */
    public function getEvaluations(int $judgeId): array
    {
        return $this->db->fetchAll(
            'SELECT e.EvaluationID, e.TotalScore, e.InnovationScore,
                    e.TechnicalScore, e.PresentationScore, e.UsabilityScore,
                    e.Comments, e.EvaluationDate,
                    p.ProjectName, t.TeamName
             FROM Evaluations e
             JOIN Projects p ON e.ProjectID = p.ProjectID
             JOIN Teams    t ON p.TeamID    = t.TeamID
             WHERE e.JudgeID = ?
             ORDER BY e.EvaluationDate DESC',
            [$judgeId]
        );
    }

    /** Lista básica para el selector de evaluaciones. */
    public function findAllBasic(): array
    {
        return $this->db->fetchAll(
            'SELECT JudgeID,
                    CONCAT(FirstName, " ", LastName) AS fullName,
                    Expertise
             FROM Judges
             ORDER BY FirstName, LastName'
        );
    }

    // ─── CRUD ───────────────────────────────────────────────────────────────────

    public function create(array $cleanData): int
    {
        $this->db->execute(
            'INSERT INTO Judges (FirstName, LastName, Email, Company, Expertise, YearsOfExperience)
             VALUES (?, ?, ?, ?, ?, ?)',
            [
                $cleanData['firstName'],
                $cleanData['lastName'],
                $cleanData['email'],
                $cleanData['company'],
                $cleanData['expertise'],
                $cleanData['yearsOfExperience'],
            ]
        );
        return $this->db->lastInsertId();
    }

    public function update(int $id, array $cleanData): int
    {
        return $this->db->execute(
            'UPDATE Judges
             SET FirstName         = ?,
                 LastName          = ?,
                 Email             = ?,
                 Company           = ?,
                 Expertise         = ?,
                 YearsOfExperience = ?
             WHERE JudgeID = ?',
            [
                $cleanData['firstName'],
                $cleanData['lastName'],
                $cleanData['email'],
                $cleanData['company'],
                $cleanData['expertise'],
                $cleanData['yearsOfExperience'],
                $id,
            ]
        );
    }

    // ─── Validación ─────────────────────────────────────────────────────────────

    public function validate(array $rawData): array
    {
        return $this->mergeErrors(
            $this->requireFields([
                'firstName' => 'Nombre',
                'lastName'  => 'Apellidos',
                'email'     => 'Email',
                'expertise' => 'Área de expertise',
            ], $rawData),
            $this->requireValidEmail('email', 'Email', $rawData),
            $this->requireNumericRange('yearsOfExperience', 'Años de experiencia', 0, 60, $rawData),
            $this->requireMaxLength('firstName', 'Nombre',    100, $rawData),
            $this->requireMaxLength('lastName',  'Apellidos', 100, $rawData),
            $this->requireMaxLength('email',     'Email',     150, $rawData)
        );
    }
}
