-- ═══════════════════════════════════════════════════════════════════════════
--  Hackathon CRM — Scripts de setup adicionales
--  Ejecutar en la base de datos: HackathonDB
--  Requiere que las tablas base ya existan (Teams, HackathonParticipants, etc.)
-- ═══════════════════════════════════════════════════════════════════════════

-- ─── 1. Tabla de usuarios administradores ────────────────────────────────────
CREATE TABLE IF NOT EXISTS AdminUsers (
    UserID       INT          NOT NULL AUTO_INCREMENT,
    Username     VARCHAR(50)  NOT NULL UNIQUE,
    PasswordHash VARCHAR(255) NOT NULL,
    Email        VARCHAR(100) NOT NULL UNIQUE,
    FullName     VARCHAR(100) NOT NULL,
    Role         ENUM('admin','manager') NOT NULL DEFAULT 'admin',
    IsActive     TINYINT(1)   NOT NULL DEFAULT 1,
    LastLogin    DATETIME         NULL,
    CreatedAt    DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (UserID),
    INDEX idx_username (Username),
    INDEX idx_email    (Email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ─── 2. Vista TeamStatistics (usada en estadísticas de equipos) ───────────────
-- Esta vista es OPCIONAL; el código PHP ya usa subqueries directas.
-- La dejamos para compatibilidad con herramientas de BI o reportes externos.
CREATE OR REPLACE VIEW TeamStatistics AS
SELECT
    t.TeamID,
    COUNT(DISTINCT tm.MemberID)              AS TotalMembers,
    COALESCE(SUM(ms.Duration) / 60, 0)      AS TotalMentoringHours,
    COUNT(DISTINCT ms.SessionID)             AS TotalMentoringSessions,
    AVG(e.TotalScore)                        AS AverageProjectScore
FROM Teams t
LEFT JOIN TeamMembers    tm ON t.TeamID = tm.TeamID
LEFT JOIN MentoringSessions ms ON t.TeamID = ms.TeamID
LEFT JOIN Projects        p  ON t.TeamID = p.TeamID
LEFT JOIN Evaluations     e  ON p.ProjectID = e.ProjectID
GROUP BY t.TeamID;

-- ─── IMPORTANTE ─────────────────────────────────────────────────────────────
-- Para crear el usuario admin, ejecuta el script PHP:
--   http://localhost/hackathon-crm/database/setup.php
-- O bien, genera el hash en PHP y sustituye HASH_BCRYPT_AQUI:
--
--   INSERT INTO AdminUsers (Username, PasswordHash, Email, FullName, Role)
--   VALUES ('admin', 'HASH_BCRYPT_AQUI', 'admin@hackathon.local', 'Administrador', 'admin');
--
-- Genera el hash con: echo password_hash('TuContraseña', PASSWORD_BCRYPT, ['cost'=>12]);
