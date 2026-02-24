<?php
declare(strict_types=1);

class AwardModel extends BaseModel
{
    protected string $table      = 'Awards';
    protected string $primaryKey = 'AwardID';

    // ─── Listado con proyecto asignado ──────────────────────────────────────────
    public function findAll(int $page = 1): array
    {
        $offset = ($page - 1) * RECORDS_PER_PAGE;
        return $this->db->fetchAll(
            'SELECT a.AwardID, a.AwardName, a.Category, a.Prize,
                    a.AwardedDate,
                    p.ProjectName, t.TeamName
             FROM Awards a
             LEFT JOIN Projects p ON a.ProjectID = p.ProjectID
             LEFT JOIN Teams    t ON p.TeamID     = t.TeamID
             ORDER BY a.AwardID DESC
             LIMIT ? OFFSET ?',
            [RECORDS_PER_PAGE, $offset]
        );
    }

    // ─── Detalle lo dejamos publica para que lo podamos llamar en cualquier sitio del codigo, "La unica fuente de la verdad" ─────────────────────────────────────────────────────────────────
    public function findById(int $id): array|false
    {
        return $this->db->fetchOne(
            'SELECT a.*,
                    p.ProjectName, t.TeamName,
                    CONCAT(hp.FirstName, " ", hp.LastName) AS leaderName
             FROM Awards a
             LEFT JOIN Projects p ON a.ProjectID = p.ProjectID
             LEFT JOIN Teams    t ON p.TeamID     = t.TeamID
             LEFT JOIN HackathonParticipants hp ON t.TeamLeaderID = hp.ParticipantID
             WHERE a.AwardID = ?',
            [$id]
        );
    }

    /** Premios sin asignar (ProjectID IS NULL) esto realmente lo que hacemos es que hacer consultas en funciones y lo inicamos como Objetos. */
    public function findUnassigned(): array
    {
        return $this->db->fetchAll(
            'SELECT AwardID, AwardName, Category, Prize
             FROM Awards
             WHERE ProjectID IS NULL
             ORDER BY AwardName'
        );
    }

    /** Proyectos elegibles para recibir un premio. el SQL es strings todas las consultas las hacemos funciones */
    public function getEligibleProjects(): array
    {
        return $this->db->fetchAll(
            'SELECT p.ProjectID,
                    CONCAT(p.ProjectName, " — ", t.TeamName) AS displayName,
                    p.Status
             FROM Projects p
             JOIN Teams t ON p.TeamID = t.TeamID
             WHERE p.Status IN ("Submitted","Under Review","Awarded")
             ORDER BY p.ProjectName'
        );
    }

    // ─── CRUD  Esto son las funciones de SQL───────────────────────────────────────────────────────────────────

    public function create(array $cleanData): int
    {
        $this->db->execute(
            'INSERT INTO Awards (AwardName, Category, Prize)
             VALUES (?, ?, ?)',
            [
                $cleanData['awardName'],
                $cleanData['category'],
                $cleanData['prize'],
            ]
        );
        return $this->db->lastInsertId();
    }

    public function update(int $id, array $cleanData): int
    {
        return $this->db->execute(
            'UPDATE Awards
             SET AwardName = ?, Category = ?, Prize = ?
             WHERE AwardID = ?',
            [
                $cleanData['awardName'],
                $cleanData['category'],
                $cleanData['prize'],
                $id,
            ]
        );
    }

    /** Asigna un premio a un proyecto (POST). */
    public function assign(int $awardId, int $projectId, string $awardedDate): int
    {
        return $this->db->execute(
            'UPDATE Awards
             SET ProjectID = ?, AwardedDate = ?
             WHERE AwardID = ?',
            [$projectId, $awardedDate, $awardId]
        );
    }

    /** Desasigna un premio (libera el proyecto). */
    public function unassign(int $awardId): int
    {
        return $this->db->execute(
            'UPDATE Awards SET ProjectID = NULL, AwardedDate = NULL WHERE AwardID = ?',
            [$awardId]
        );
    }

    // ─── Validación ─────────────────────────────────────────────────────────────

    public function validate(array $rawData): array
    {
        return $this->mergeErrors(
            $this->requireFields([
                'awardName' => 'Nombre del premio',
                'category'  => 'Categoría',
            ], $rawData),
            $this->requireMaxLength('awardName', 'Nombre del premio', 150, $rawData)
        );
    }

    public function validateAssign(array $rawData): array
    {
        return $this->requireFields([
            'projectId'   => 'Proyecto',
            'awardedDate' => 'Fecha de entrega',
        ], $rawData);
    }
}
