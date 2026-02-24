-- ============================================================
-- BASE DE DATOS: SISTEMA DE GESTIÓN DE HACKATHON
-- Versión completa adaptada para MySQL Workbench
-- ============================================================
-- Este script crea y popula la base de datos completa para
-- gestionar hackathons: participantes, equipos, proyectos,
-- mentores, jueces, evaluaciones, premios y usuarios admin.
--
-- REQUISITOS:
--   · MySQL 8.0.13 o superior (DEFAULT expressions en columnas)
--   · Ejecutar como usuario con permisos CREATE/DROP DATABASE
--
-- USO EN WORKBENCH:
--   1. Abrir este fichero con File > Open SQL Script
--   2. Ejecutar con el rayo (Execute All) o Ctrl+Shift+Enter
--   3. El script es idempotente: puede re-ejecutarse borrando
--      y recreando toda la base de datos desde cero.
-- ============================================================

-- ============================================================
-- CONFIGURACIÓN DE SESIÓN (Workbench-safe)
-- ============================================================
SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS,   UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION';
SET NAMES utf8mb4;
SET CHARACTER SET utf8mb4;

-- ============================================================
-- SECCIÓN 0: BASE DE DATOS
-- ============================================================
DROP DATABASE IF EXISTS HackathonDB;
CREATE DATABASE HackathonDB
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE HackathonDB;

-- ============================================================
-- SECCIÓN 1: CREACIÓN DE TABLAS
-- ============================================================

-- ------------------------------------------------------------
-- Tabla: HackathonParticipants
-- Descripción: Almacena información de los participantes
-- ------------------------------------------------------------
CREATE TABLE HackathonParticipants (
    ParticipantID    INT          NOT NULL AUTO_INCREMENT,
    FirstName        VARCHAR(100) NOT NULL,
    LastName         VARCHAR(100) NOT NULL,
    Email            VARCHAR(150) NOT NULL UNIQUE,
    Phone            VARCHAR(20),
    University       VARCHAR(200),
    Major            VARCHAR(100),
    YearOfStudy      INT          CHECK (YearOfStudy BETWEEN 1 AND 10),
    RegistrationDate DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    LastUpdated      DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    PRIMARY KEY (ParticipantID),
    INDEX idx_university (University),
    INDEX idx_email      (Email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- Tabla: AuditLog
-- Descripción: Registro de auditoría para cambios en la BD
-- NOTA: DEFAULT (CURRENT_USER()) requiere MySQL 8.0.13+
-- ------------------------------------------------------------
CREATE TABLE AuditLog (
    LogID      INT                              NOT NULL AUTO_INCREMENT,
    TableName  VARCHAR(100)                     NOT NULL,
    RecordID   INT                              NOT NULL,
    Action     ENUM('INSERT', 'UPDATE', 'DELETE') NOT NULL,
    OldValues  TEXT,
    NewValues  TEXT,
    ChangedBy  VARCHAR(100)                     DEFAULT (CURRENT_USER()),
    ChangeDate DATETIME                         NOT NULL DEFAULT CURRENT_TIMESTAMP,

    PRIMARY KEY (LogID),
    INDEX idx_table_record (TableName, RecordID),
    INDEX idx_action       (Action),
    INDEX idx_change_date  (ChangeDate)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- Tabla: Teams
-- Descripción: Equipos participantes en el hackathon
-- ------------------------------------------------------------
CREATE TABLE Teams (
    TeamID       INT          NOT NULL AUTO_INCREMENT,
    TeamName     VARCHAR(100) NOT NULL UNIQUE,
    TeamLeaderID INT,
    MaxMembers   INT          NOT NULL DEFAULT 5 CHECK (MaxMembers BETWEEN 2 AND 10),
    CreatedDate  DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,

    PRIMARY KEY (TeamID),
    INDEX idx_leader (TeamLeaderID),

    CONSTRAINT fk_team_leader
        FOREIGN KEY (TeamLeaderID)
        REFERENCES HackathonParticipants(ParticipantID)
        ON DELETE SET NULL
        ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- Tabla: Mentors
-- Descripción: Mentores disponibles para los equipos
-- ------------------------------------------------------------
CREATE TABLE Mentors (
    MentorID       INT          NOT NULL AUTO_INCREMENT,
    FirstName      VARCHAR(100) NOT NULL,
    LastName       VARCHAR(100) NOT NULL,
    Email          VARCHAR(150) NOT NULL UNIQUE,
    Company        VARCHAR(200),
    Specialization VARCHAR(200),
    AvailableSlots INT          NOT NULL DEFAULT 5 CHECK (AvailableSlots >= 0),

    PRIMARY KEY (MentorID),
    INDEX idx_specialization (Specialization),
    INDEX idx_company        (Company)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- Tabla: TeamMembers
-- Descripción: Relación N:M entre participantes y equipos
-- ------------------------------------------------------------
CREATE TABLE TeamMembers (
    TeamMemberID  INT         NOT NULL AUTO_INCREMENT,
    TeamID        INT         NOT NULL,
    ParticipantID INT         NOT NULL,
    JoinedDate    DATETIME    NOT NULL DEFAULT CURRENT_TIMESTAMP,
    Role          VARCHAR(50) NOT NULL DEFAULT 'Member',

    PRIMARY KEY (TeamMemberID),
    UNIQUE KEY uk_team_participant (TeamID, ParticipantID),
    INDEX idx_participant (ParticipantID),

    CONSTRAINT fk_tm_team
        FOREIGN KEY (TeamID)
        REFERENCES Teams(TeamID)
        ON DELETE CASCADE
        ON UPDATE CASCADE,

    CONSTRAINT fk_tm_participant
        FOREIGN KEY (ParticipantID)
        REFERENCES HackathonParticipants(ParticipantID)
        ON DELETE CASCADE
        ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- Tabla: Judges
-- Descripción: Jueces que evalúan los proyectos
-- ------------------------------------------------------------
CREATE TABLE Judges (
    JudgeID           INT          NOT NULL AUTO_INCREMENT,
    FirstName         VARCHAR(100) NOT NULL,
    LastName          VARCHAR(100) NOT NULL,
    Email             VARCHAR(150) NOT NULL UNIQUE,
    Company           VARCHAR(200),
    Expertise         VARCHAR(200),
    YearsOfExperience INT          NOT NULL DEFAULT 0 CHECK (YearsOfExperience >= 0),
    CreatedDate       DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,

    PRIMARY KEY (JudgeID),
    INDEX idx_expertise (Expertise),
    INDEX idx_company   (Company)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- Tabla: Projects
-- Descripción: Proyectos desarrollados por los equipos
-- ------------------------------------------------------------
CREATE TABLE Projects (
    ProjectID       INT          NOT NULL AUTO_INCREMENT,
    TeamID          INT          NOT NULL,
    ProjectName     VARCHAR(200) NOT NULL,
    Description     TEXT,
    Category        VARCHAR(100),
    TechnologyStack TEXT,
    GitHubURL       VARCHAR(500),
    DemoURL         VARCHAR(500),
    SubmissionDate  DATETIME,
    Status          ENUM('In Progress','Submitted','Under Review','Evaluated','Awarded')
                    NOT NULL DEFAULT 'In Progress',

    PRIMARY KEY (ProjectID),
    INDEX idx_team     (TeamID),
    INDEX idx_category (Category),
    INDEX idx_status   (Status),

    CONSTRAINT fk_project_team
        FOREIGN KEY (TeamID)
        REFERENCES Teams(TeamID)
        ON DELETE CASCADE
        ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- Tabla: MentoringSessions
-- Descripción: Sesiones de mentoría entre mentores y equipos
-- ------------------------------------------------------------
CREATE TABLE MentoringSessions (
    SessionID   INT          NOT NULL AUTO_INCREMENT,
    MentorID    INT          NOT NULL,
    TeamID      INT          NOT NULL,
    SessionDate DATETIME     NOT NULL,
    Duration    INT          NOT NULL DEFAULT 60 COMMENT 'Duración en minutos',
    Topic       VARCHAR(200),
    Notes       TEXT,

    PRIMARY KEY (SessionID),
    INDEX idx_mentor       (MentorID),
    INDEX idx_team         (TeamID),
    INDEX idx_session_date (SessionDate),

    CONSTRAINT fk_ms_mentor
        FOREIGN KEY (MentorID)
        REFERENCES Mentors(MentorID)
        ON DELETE CASCADE
        ON UPDATE CASCADE,

    CONSTRAINT fk_ms_team
        FOREIGN KEY (TeamID)
        REFERENCES Teams(TeamID)
        ON DELETE CASCADE
        ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- Tabla: TeamStatistics
-- Descripción: Estadísticas agregadas de cada equipo
--              Mantenida automáticamente por triggers
-- ------------------------------------------------------------
CREATE TABLE TeamStatistics (
    TeamID                  INT            NOT NULL,
    TotalMembers            INT            NOT NULL DEFAULT 0,
    TotalMentoringSessions  INT            NOT NULL DEFAULT 0,
    TotalMentoringHours     DECIMAL(10,2)  NOT NULL DEFAULT 0.00,
    AverageProjectScore     DECIMAL(5,2)   NOT NULL DEFAULT 0.00,
    LastUpdated             DATETIME       NOT NULL DEFAULT CURRENT_TIMESTAMP
                            ON UPDATE CURRENT_TIMESTAMP,

    PRIMARY KEY (TeamID),

    CONSTRAINT fk_stats_team
        FOREIGN KEY (TeamID)
        REFERENCES Teams(TeamID)
        ON DELETE CASCADE
        ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- Tabla: Evaluations
-- Descripción: Evaluaciones de proyectos por jueces
-- ------------------------------------------------------------
CREATE TABLE Evaluations (
    EvaluationID      INT           NOT NULL AUTO_INCREMENT,
    ProjectID         INT           NOT NULL,
    JudgeID           INT           NOT NULL,
    InnovationScore   DECIMAL(4,2)  CHECK (InnovationScore   BETWEEN 0 AND 10),
    TechnicalScore    DECIMAL(4,2)  CHECK (TechnicalScore    BETWEEN 0 AND 10),
    PresentationScore DECIMAL(4,2)  CHECK (PresentationScore BETWEEN 0 AND 10),
    UsabilityScore    DECIMAL(4,2)  CHECK (UsabilityScore    BETWEEN 0 AND 10),
    TotalScore        DECIMAL(5,2)  GENERATED ALWAYS AS (
        (COALESCE(InnovationScore,   0) +
         COALESCE(TechnicalScore,    0) +
         COALESCE(PresentationScore, 0) +
         COALESCE(UsabilityScore,    0)) / 4
    ) STORED,
    Comments          TEXT,
    EvaluationDate    DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP,

    PRIMARY KEY (EvaluationID),
    UNIQUE KEY uk_project_judge (ProjectID, JudgeID),
    INDEX idx_project     (ProjectID),
    INDEX idx_judge       (JudgeID),
    INDEX idx_total_score (TotalScore),

    CONSTRAINT fk_eval_project
        FOREIGN KEY (ProjectID)
        REFERENCES Projects(ProjectID)
        ON DELETE CASCADE
        ON UPDATE CASCADE,

    CONSTRAINT fk_eval_judge
        FOREIGN KEY (JudgeID)
        REFERENCES Judges(JudgeID)
        ON DELETE CASCADE
        ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- Tabla: Awards
-- Descripción: Premios otorgados a proyectos
-- ------------------------------------------------------------
CREATE TABLE Awards (
    AwardID     INT          NOT NULL AUTO_INCREMENT,
    AwardName   VARCHAR(200) NOT NULL,
    Category    VARCHAR(100),
    Prize       VARCHAR(200),
    ProjectID   INT,
    AwardedDate DATETIME,

    PRIMARY KEY (AwardID),
    INDEX idx_project  (ProjectID),
    INDEX idx_category (Category),

    CONSTRAINT fk_award_project
        FOREIGN KEY (ProjectID)
        REFERENCES Projects(ProjectID)
        ON DELETE SET NULL
        ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- Tabla: EventLog
-- Descripción: Registro de eventos del sistema
-- ------------------------------------------------------------
CREATE TABLE EventLog (
    EventID          INT          NOT NULL AUTO_INCREMENT,
    EventType        VARCHAR(100) NOT NULL,
    Description      TEXT,
    RelatedTeamID    INT,
    RelatedProjectID INT,
    EventDate        DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,

    PRIMARY KEY (EventID),
    INDEX idx_event_type (EventType),
    INDEX idx_team       (RelatedTeamID),
    INDEX idx_project    (RelatedProjectID),
    INDEX idx_event_date (EventDate),

    CONSTRAINT fk_event_team
        FOREIGN KEY (RelatedTeamID)
        REFERENCES Teams(TeamID)
        ON DELETE SET NULL
        ON UPDATE CASCADE,

    CONSTRAINT fk_event_project
        FOREIGN KEY (RelatedProjectID)
        REFERENCES Projects(ProjectID)
        ON DELETE SET NULL
        ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- Tabla: AdminUsers  ★ SISTEMA DE AUTENTICACIÓN ★
-- Descripción: Usuarios administradores del CRM.
--              Las contraseñas se almacenan como hash bcrypt
--              (gestionadas por PHP con password_hash/verify).
-- ------------------------------------------------------------
CREATE TABLE AdminUsers (
    UserID       INT                      NOT NULL AUTO_INCREMENT,
    Username     VARCHAR(50)              NOT NULL,
    PasswordHash VARCHAR(255)             NOT NULL  COMMENT 'Hash bcrypt generado por PHP',
    Email        VARCHAR(100)             NOT NULL,
    FullName     VARCHAR(100)             NOT NULL,
    Role         ENUM('admin','manager')  NOT NULL DEFAULT 'admin',
    IsActive     TINYINT(1)               NOT NULL DEFAULT 1,
    LastLogin    DATETIME,
    CreatedAt    DATETIME                 NOT NULL DEFAULT CURRENT_TIMESTAMP,

    PRIMARY KEY (UserID),
    UNIQUE KEY uk_username (Username),
    UNIQUE KEY uk_email    (Email),
    INDEX idx_username     (Username),
    INDEX idx_email        (Email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- SECCIÓN 2: TRIGGERS
-- ============================================================
DELIMITER //

-- ------------------------------------------------------------
-- TRIGGERS DE AUDITORÍA — HackathonParticipants
-- ------------------------------------------------------------

CREATE TRIGGER trg_participants_insert_audit
AFTER INSERT ON HackathonParticipants
FOR EACH ROW
BEGIN
    INSERT INTO AuditLog (TableName, RecordID, Action, NewValues)
    VALUES (
        'HackathonParticipants',
        NEW.ParticipantID,
        'INSERT',
        JSON_OBJECT(
            'ParticipantID', NEW.ParticipantID,
            'FirstName',     NEW.FirstName,
            'LastName',      NEW.LastName,
            'Email',         NEW.Email,
            'University',    NEW.University,
            'Major',         NEW.Major
        )
    );
END//

CREATE TRIGGER trg_participants_update_audit
AFTER UPDATE ON HackathonParticipants
FOR EACH ROW
BEGIN
    INSERT INTO AuditLog (TableName, RecordID, Action, OldValues, NewValues)
    VALUES (
        'HackathonParticipants',
        NEW.ParticipantID,
        'UPDATE',
        JSON_OBJECT(
            'FirstName',  OLD.FirstName,
            'LastName',   OLD.LastName,
            'Email',      OLD.Email,
            'University', OLD.University,
            'Major',      OLD.Major
        ),
        JSON_OBJECT(
            'FirstName',  NEW.FirstName,
            'LastName',   NEW.LastName,
            'Email',      NEW.Email,
            'University', NEW.University,
            'Major',      NEW.Major
        )
    );
END//

CREATE TRIGGER trg_participants_delete_audit
BEFORE DELETE ON HackathonParticipants
FOR EACH ROW
BEGIN
    INSERT INTO AuditLog (TableName, RecordID, Action, OldValues)
    VALUES (
        'HackathonParticipants',
        OLD.ParticipantID,
        'DELETE',
        JSON_OBJECT(
            'ParticipantID', OLD.ParticipantID,
            'FirstName',     OLD.FirstName,
            'LastName',      OLD.LastName,
            'Email',         OLD.Email,
            'University',    OLD.University,
            'Major',         OLD.Major
        )
    );
END//

-- ------------------------------------------------------------
-- TRIGGERS PARA GESTIÓN DE EQUIPOS
-- ------------------------------------------------------------

CREATE TRIGGER trg_team_after_insert
AFTER INSERT ON Teams
FOR EACH ROW
BEGIN
    INSERT INTO TeamStatistics (TeamID, TotalMembers, TotalMentoringSessions,
                                TotalMentoringHours, AverageProjectScore)
    VALUES (NEW.TeamID, 0, 0, 0.00, 0.00);

    INSERT INTO EventLog (EventType, Description, RelatedTeamID)
    VALUES ('TEAM_CREATED', CONCAT('Nuevo equipo creado: ', NEW.TeamName), NEW.TeamID);
END//

CREATE TRIGGER trg_teams_update_audit
AFTER UPDATE ON Teams
FOR EACH ROW
BEGIN
    INSERT INTO AuditLog (TableName, RecordID, Action, OldValues, NewValues)
    VALUES (
        'Teams',
        NEW.TeamID,
        'UPDATE',
        JSON_OBJECT('TeamName', OLD.TeamName, 'TeamLeaderID', OLD.TeamLeaderID, 'MaxMembers', OLD.MaxMembers),
        JSON_OBJECT('TeamName', NEW.TeamName, 'TeamLeaderID', NEW.TeamLeaderID, 'MaxMembers', NEW.MaxMembers)
    );
END//

-- ------------------------------------------------------------
-- TRIGGERS PARA MIEMBROS DE EQUIPO
-- ------------------------------------------------------------

CREATE TRIGGER trg_teammembers_before_insert
BEFORE INSERT ON TeamMembers
FOR EACH ROW
BEGIN
    DECLARE current_members INT;
    DECLARE max_allowed     INT;

    SELECT COUNT(*), t.MaxMembers
    INTO   current_members, max_allowed
    FROM   Teams t
    LEFT JOIN TeamMembers tm ON t.TeamID = tm.TeamID
    WHERE  t.TeamID = NEW.TeamID
    GROUP BY t.TeamID, t.MaxMembers;

    IF current_members >= max_allowed THEN
        SIGNAL SQLSTATE '45000'
            SET MESSAGE_TEXT = 'Error: El equipo ha alcanzado el número máximo de miembros';
    END IF;
END//

CREATE TRIGGER trg_teammembers_after_insert
AFTER INSERT ON TeamMembers
FOR EACH ROW
BEGIN
    UPDATE TeamStatistics
    SET    TotalMembers = (SELECT COUNT(*) FROM TeamMembers WHERE TeamID = NEW.TeamID)
    WHERE  TeamID = NEW.TeamID;

    INSERT INTO EventLog (EventType, Description, RelatedTeamID)
    VALUES ('MEMBER_JOINED',
            CONCAT('Nuevo miembro (ID: ', NEW.ParticipantID, ') se unió al equipo'),
            NEW.TeamID);
END//

CREATE TRIGGER trg_teammembers_after_delete
AFTER DELETE ON TeamMembers
FOR EACH ROW
BEGIN
    UPDATE TeamStatistics
    SET    TotalMembers = (SELECT COUNT(*) FROM TeamMembers WHERE TeamID = OLD.TeamID)
    WHERE  TeamID = OLD.TeamID;

    INSERT INTO EventLog (EventType, Description, RelatedTeamID)
    VALUES ('MEMBER_LEFT',
            CONCAT('Miembro (ID: ', OLD.ParticipantID, ') dejó el equipo'),
            OLD.TeamID);
END//

-- ------------------------------------------------------------
-- TRIGGERS PARA SESIONES DE MENTORÍA
-- ------------------------------------------------------------

CREATE TRIGGER trg_mentoring_before_insert
BEFORE INSERT ON MentoringSessions
FOR EACH ROW
BEGIN
    DECLARE available INT;

    SELECT AvailableSlots INTO available
    FROM   Mentors
    WHERE  MentorID = NEW.MentorID;

    IF available <= 0 THEN
        SIGNAL SQLSTATE '45000'
            SET MESSAGE_TEXT = 'Error: El mentor no tiene slots disponibles';
    END IF;
END//

CREATE TRIGGER trg_mentoring_after_insert
AFTER INSERT ON MentoringSessions
FOR EACH ROW
BEGIN
    UPDATE Mentors
    SET    AvailableSlots = AvailableSlots - 1
    WHERE  MentorID = NEW.MentorID;

    UPDATE TeamStatistics
    SET    TotalMentoringSessions = TotalMentoringSessions + 1,
           TotalMentoringHours    = TotalMentoringHours + (NEW.Duration / 60.0)
    WHERE  TeamID = NEW.TeamID;

    INSERT INTO EventLog (EventType, Description, RelatedTeamID)
    VALUES ('MENTORING_SESSION',
            CONCAT('Sesión de mentoría programada: ', COALESCE(NEW.Topic, 'Sin tema')),
            NEW.TeamID);
END//

CREATE TRIGGER trg_mentoring_after_delete
AFTER DELETE ON MentoringSessions
FOR EACH ROW
BEGIN
    UPDATE Mentors
    SET    AvailableSlots = AvailableSlots + 1
    WHERE  MentorID = OLD.MentorID;

    UPDATE TeamStatistics
    SET    TotalMentoringSessions = TotalMentoringSessions - 1,
           TotalMentoringHours    = TotalMentoringHours - (OLD.Duration / 60.0)
    WHERE  TeamID = OLD.TeamID;
END//

-- ------------------------------------------------------------
-- TRIGGERS PARA PROYECTOS
-- ------------------------------------------------------------

CREATE TRIGGER trg_project_after_insert
AFTER INSERT ON Projects
FOR EACH ROW
BEGIN
    INSERT INTO EventLog (EventType, Description, RelatedTeamID, RelatedProjectID)
    VALUES ('PROJECT_CREATED',
            CONCAT('Nuevo proyecto creado: ', NEW.ProjectName),
            NEW.TeamID, NEW.ProjectID);
END//

CREATE TRIGGER trg_project_after_update
AFTER UPDATE ON Projects
FOR EACH ROW
BEGIN
    IF OLD.Status != NEW.Status THEN
        INSERT INTO EventLog (EventType, Description, RelatedTeamID, RelatedProjectID)
        VALUES ('PROJECT_STATUS_CHANGED',
                CONCAT('Estado del proyecto cambió de "', OLD.Status, '" a "', NEW.Status, '"'),
                NEW.TeamID, NEW.ProjectID);
    END IF;

    IF OLD.SubmissionDate IS NULL AND NEW.SubmissionDate IS NOT NULL THEN
        INSERT INTO EventLog (EventType, Description, RelatedTeamID, RelatedProjectID)
        VALUES ('PROJECT_SUBMITTED',
                CONCAT('Proyecto "', NEW.ProjectName, '" enviado para evaluación'),
                NEW.TeamID, NEW.ProjectID);
    END IF;

    INSERT INTO AuditLog (TableName, RecordID, Action, OldValues, NewValues)
    VALUES (
        'Projects', NEW.ProjectID, 'UPDATE',
        JSON_OBJECT('ProjectName', OLD.ProjectName, 'Status', OLD.Status, 'Category', OLD.Category),
        JSON_OBJECT('ProjectName', NEW.ProjectName, 'Status', NEW.Status, 'Category', NEW.Category)
    );
END//

-- ------------------------------------------------------------
-- TRIGGERS PARA EVALUACIONES
-- ------------------------------------------------------------

CREATE TRIGGER trg_evaluation_after_insert
AFTER INSERT ON Evaluations
FOR EACH ROW
BEGIN
    DECLARE team_id   INT;
    DECLARE avg_score DECIMAL(5,2);

    SELECT TeamID INTO team_id FROM Projects WHERE ProjectID = NEW.ProjectID;

    SELECT AVG(e.TotalScore) INTO avg_score
    FROM   Evaluations e
    JOIN   Projects    p ON e.ProjectID = p.ProjectID
    WHERE  p.TeamID = team_id;

    UPDATE TeamStatistics
    SET    AverageProjectScore = COALESCE(avg_score, 0)
    WHERE  TeamID = team_id;

    UPDATE Projects
    SET    Status = 'Under Review'
    WHERE  ProjectID = NEW.ProjectID AND Status = 'Submitted';

    INSERT INTO EventLog (EventType, Description, RelatedTeamID, RelatedProjectID)
    VALUES ('PROJECT_EVALUATED',
            CONCAT('Proyecto evaluado por juez ID: ', NEW.JudgeID,
                   ' — Puntuación: ', NEW.TotalScore),
            team_id, NEW.ProjectID);
END//

CREATE TRIGGER trg_evaluation_after_update
AFTER UPDATE ON Evaluations
FOR EACH ROW
BEGIN
    DECLARE team_id   INT;
    DECLARE avg_score DECIMAL(5,2);

    SELECT TeamID INTO team_id FROM Projects WHERE ProjectID = NEW.ProjectID;

    SELECT AVG(e.TotalScore) INTO avg_score
    FROM   Evaluations e
    JOIN   Projects    p ON e.ProjectID = p.ProjectID
    WHERE  p.TeamID = team_id;

    UPDATE TeamStatistics
    SET    AverageProjectScore = COALESCE(avg_score, 0)
    WHERE  TeamID = team_id;
END//

CREATE TRIGGER trg_evaluation_after_delete
AFTER DELETE ON Evaluations
FOR EACH ROW
BEGIN
    DECLARE team_id   INT;
    DECLARE avg_score DECIMAL(5,2);

    SELECT TeamID INTO team_id FROM Projects WHERE ProjectID = OLD.ProjectID;

    SELECT AVG(e.TotalScore) INTO avg_score
    FROM   Evaluations e
    JOIN   Projects    p ON e.ProjectID = p.ProjectID
    WHERE  p.TeamID = team_id;

    UPDATE TeamStatistics
    SET    AverageProjectScore = COALESCE(avg_score, 0)
    WHERE  TeamID = team_id;
END//

-- ------------------------------------------------------------
-- TRIGGERS PARA PREMIOS
-- ------------------------------------------------------------

CREATE TRIGGER trg_award_after_insert
AFTER INSERT ON Awards
FOR EACH ROW
BEGIN
    DECLARE team_id INT;

    IF NEW.ProjectID IS NOT NULL THEN
        SELECT TeamID INTO team_id FROM Projects WHERE ProjectID = NEW.ProjectID;

        UPDATE Projects SET Status = 'Awarded' WHERE ProjectID = NEW.ProjectID;

        INSERT INTO EventLog (EventType, Description, RelatedTeamID, RelatedProjectID)
        VALUES ('AWARD_GRANTED',
                CONCAT('Premio otorgado: ', NEW.AwardName, ' — ', COALESCE(NEW.Prize, 'Sin especificar')),
                team_id, NEW.ProjectID);
    END IF;
END//

-- ------------------------------------------------------------
-- TRIGGER DE AUDITORÍA — AdminUsers  ★ NUEVO ★
-- Registra en AuditLog los cambios sobre los usuarios admin.
-- Las contraseñas NO se guardan en el log (solo se anota el cambio).
-- ------------------------------------------------------------

CREATE TRIGGER trg_adminusers_insert_audit
AFTER INSERT ON AdminUsers
FOR EACH ROW
BEGIN
    INSERT INTO AuditLog (TableName, RecordID, Action, NewValues)
    VALUES (
        'AdminUsers',
        NEW.UserID,
        'INSERT',
        JSON_OBJECT(
            'UserID',   NEW.UserID,
            'Username', NEW.Username,
            'Email',    NEW.Email,
            'FullName', NEW.FullName,
            'Role',     NEW.Role,
            'IsActive', NEW.IsActive
        )
    );
END//

CREATE TRIGGER trg_adminusers_update_audit
AFTER UPDATE ON AdminUsers
FOR EACH ROW
BEGIN
    INSERT INTO AuditLog (TableName, RecordID, Action, OldValues, NewValues)
    VALUES (
        'AdminUsers',
        NEW.UserID,
        'UPDATE',
        JSON_OBJECT(
            'Username', OLD.Username,
            'Email',    OLD.Email,
            'FullName', OLD.FullName,
            'Role',     OLD.Role,
            'IsActive', OLD.IsActive,
            'password_changed', IF(OLD.PasswordHash != NEW.PasswordHash, 'YES', 'NO')
        ),
        JSON_OBJECT(
            'Username', NEW.Username,
            'Email',    NEW.Email,
            'FullName', NEW.FullName,
            'Role',     NEW.Role,
            'IsActive', NEW.IsActive,
            'password_changed', IF(OLD.PasswordHash != NEW.PasswordHash, 'YES', 'NO')
        )
    );

    -- Registrar evento especial si se actualizó el último login
    IF OLD.LastLogin IS DISTINCT FROM NEW.LastLogin THEN
        INSERT INTO EventLog (EventType, Description)
        VALUES ('ADMIN_LOGIN',
                CONCAT('Login de administrador: ', NEW.Username,
                       ' — ', DATE_FORMAT(NEW.LastLogin, '%Y-%m-%d %H:%i:%s')));
    END IF;
END//

CREATE TRIGGER trg_adminusers_delete_audit
BEFORE DELETE ON AdminUsers
FOR EACH ROW
BEGIN
    INSERT INTO AuditLog (TableName, RecordID, Action, OldValues)
    VALUES (
        'AdminUsers',
        OLD.UserID,
        'DELETE',
        JSON_OBJECT(
            'UserID',   OLD.UserID,
            'Username', OLD.Username,
            'Email',    OLD.Email,
            'FullName', OLD.FullName,
            'Role',     OLD.Role
        )
    );
END//

DELIMITER ;

-- ============================================================
-- SECCIÓN 3: VISTAS ÚTILES
-- ============================================================

-- Vista: Resumen completo de equipos
CREATE VIEW vw_team_summary AS
SELECT
    t.TeamID,
    t.TeamName,
    CONCAT(hp.FirstName, ' ', hp.LastName) AS TeamLeader,
    hp.Email                               AS LeaderEmail,
    ts.TotalMembers,
    ts.TotalMentoringSessions,
    ts.TotalMentoringHours,
    ts.AverageProjectScore,
    p.ProjectName,
    p.Status                               AS ProjectStatus
FROM Teams t
LEFT JOIN HackathonParticipants hp ON t.TeamLeaderID = hp.ParticipantID
LEFT JOIN TeamStatistics         ts ON t.TeamID       = ts.TeamID
LEFT JOIN Projects               p  ON t.TeamID       = p.TeamID;

-- Vista: Ranking de proyectos por puntuación
CREATE VIEW vw_project_rankings AS
SELECT
    p.ProjectID,
    p.ProjectName,
    t.TeamName,
    p.Category,
    p.Status,
    ROUND(AVG(e.TotalScore), 2)      AS AvgScore,
    COUNT(e.EvaluationID)            AS TotalEvaluations,
    ROUND(AVG(e.InnovationScore), 2) AS AvgInnovation,
    ROUND(AVG(e.TechnicalScore), 2)  AS AvgTechnical,
    ROUND(AVG(e.PresentationScore),2)AS AvgPresentation,
    ROUND(AVG(e.UsabilityScore), 2)  AS AvgUsability
FROM Projects p
JOIN Teams t ON p.TeamID = t.TeamID
LEFT JOIN Evaluations e ON p.ProjectID = e.ProjectID
GROUP BY p.ProjectID, p.ProjectName, t.TeamName, p.Category, p.Status
ORDER BY AvgScore DESC;

-- Vista: Actividad de mentores
CREATE VIEW vw_mentor_activity AS
SELECT
    m.MentorID,
    CONCAT(m.FirstName, ' ', m.LastName) AS MentorName,
    m.Company,
    m.Specialization,
    m.AvailableSlots,
    COUNT(ms.SessionID)                  AS TotalSessions,
    COALESCE(SUM(ms.Duration), 0)        AS TotalMinutes,
    ROUND(COALESCE(SUM(ms.Duration), 0) / 60.0, 2) AS TotalHours
FROM Mentors m
LEFT JOIN MentoringSessions ms ON m.MentorID = ms.MentorID
GROUP BY m.MentorID, m.FirstName, m.LastName, m.Company, m.Specialization, m.AvailableSlots;

-- Vista: Historial de eventos recientes
CREATE VIEW vw_recent_events AS
SELECT
    el.EventID,
    el.EventType,
    el.Description,
    t.TeamName,
    p.ProjectName,
    el.EventDate
FROM EventLog el
LEFT JOIN Teams    t ON el.RelatedTeamID    = t.TeamID
LEFT JOIN Projects p ON el.RelatedProjectID = p.ProjectID
ORDER BY el.EventDate DESC;

-- Vista: Usuarios administradores activos  ★ NUEVA ★
-- No expone PasswordHash (nunca se usa fuera del modelo PHP)
CREATE VIEW vw_admin_users AS
SELECT
    UserID,
    Username,
    Email,
    FullName,
    Role,
    IsActive,
    LastLogin,
    CreatedAt
FROM AdminUsers
ORDER BY CreatedAt;

-- ============================================================
-- SECCIÓN 4: PROCEDIMIENTOS ALMACENADOS
-- ============================================================
DELIMITER //

-- Registrar participante y agregarlo a un equipo
CREATE PROCEDURE sp_register_and_join_team(
    IN p_first_name    VARCHAR(100),
    IN p_last_name     VARCHAR(100),
    IN p_email         VARCHAR(150),
    IN p_phone         VARCHAR(20),
    IN p_university    VARCHAR(200),
    IN p_major         VARCHAR(100),
    IN p_year_of_study INT,
    IN p_team_id       INT,
    IN p_role          VARCHAR(50)
)
BEGIN
    DECLARE new_participant_id INT;

    START TRANSACTION;

    INSERT INTO HackathonParticipants
        (FirstName, LastName, Email, Phone, University, Major, YearOfStudy)
    VALUES
        (p_first_name, p_last_name, p_email, p_phone, p_university, p_major, p_year_of_study);

    SET new_participant_id = LAST_INSERT_ID();

    INSERT INTO TeamMembers (TeamID, ParticipantID, Role)
    VALUES (p_team_id, new_participant_id, COALESCE(p_role, 'Member'));

    COMMIT;

    SELECT new_participant_id AS ParticipantID, 'Registro exitoso' AS Message;
END//

-- Crear equipo completo con líder
CREATE PROCEDURE sp_create_team_with_leader(
    IN p_team_name   VARCHAR(100),
    IN p_leader_id   INT,
    IN p_max_members INT
)
BEGIN
    DECLARE new_team_id INT;

    START TRANSACTION;

    INSERT INTO Teams (TeamName, TeamLeaderID, MaxMembers)
    VALUES (p_team_name, p_leader_id, COALESCE(p_max_members, 5));

    SET new_team_id = LAST_INSERT_ID();

    INSERT INTO TeamMembers (TeamID, ParticipantID, Role)
    VALUES (new_team_id, p_leader_id, 'Leader');

    COMMIT;

    SELECT new_team_id AS TeamID, p_team_name AS TeamName,
           'Equipo creado exitosamente' AS Message;
END//

-- Evaluar proyecto
CREATE PROCEDURE sp_evaluate_project(
    IN p_project_id   INT,
    IN p_judge_id     INT,
    IN p_innovation   DECIMAL(4,2),
    IN p_technical    DECIMAL(4,2),
    IN p_presentation DECIMAL(4,2),
    IN p_usability    DECIMAL(4,2),
    IN p_comments     TEXT
)
BEGIN
    INSERT INTO Evaluations
        (ProjectID, JudgeID, InnovationScore, TechnicalScore,
         PresentationScore, UsabilityScore, Comments)
    VALUES
        (p_project_id, p_judge_id, p_innovation, p_technical,
         p_presentation, p_usability, p_comments);

    SELECT LAST_INSERT_ID()                                       AS EvaluationID,
           'Evaluación registrada'                                AS Message,
           (p_innovation + p_technical + p_presentation + p_usability) / 4 AS TotalScore;
END//

-- Obtener reporte completo de un equipo
CREATE PROCEDURE sp_team_full_report(IN p_team_id INT)
BEGIN
    SELECT 'INFORMACIÓN DEL EQUIPO' AS Section;
    SELECT t.TeamID, t.TeamName,
           CONCAT(hp.FirstName, ' ', hp.LastName) AS TeamLeader,
           t.MaxMembers, t.CreatedDate
    FROM Teams t
    LEFT JOIN HackathonParticipants hp ON t.TeamLeaderID = hp.ParticipantID
    WHERE t.TeamID = p_team_id;

    SELECT 'MIEMBROS DEL EQUIPO' AS Section;
    SELECT hp.ParticipantID,
           CONCAT(hp.FirstName, ' ', hp.LastName) AS MemberName,
           hp.Email, hp.University, tm.Role, tm.JoinedDate
    FROM TeamMembers tm
    JOIN HackathonParticipants hp ON tm.ParticipantID = hp.ParticipantID
    WHERE tm.TeamID = p_team_id;

    SELECT 'PROYECTOS' AS Section;
    SELECT p.ProjectID, p.ProjectName, p.Category, p.Status,
           p.SubmissionDate, ts.AverageProjectScore
    FROM Projects p
    JOIN TeamStatistics ts ON p.TeamID = ts.TeamID
    WHERE p.TeamID = p_team_id;

    SELECT 'SESIONES DE MENTORÍA' AS Section;
    SELECT ms.SessionID,
           CONCAT(m.FirstName, ' ', m.LastName) AS MentorName,
           m.Specialization, ms.SessionDate, ms.Duration, ms.Topic
    FROM MentoringSessions ms
    JOIN Mentors m ON ms.MentorID = m.MentorID
    WHERE ms.TeamID = p_team_id
    ORDER BY ms.SessionDate;

    SELECT 'ESTADÍSTICAS' AS Section;
    SELECT * FROM TeamStatistics WHERE TeamID = p_team_id;
END//

DELIMITER ;

-- ============================================================
-- SECCIÓN 5: DATOS DE EJEMPLO
-- ============================================================

-- Participantes
INSERT INTO HackathonParticipants
    (FirstName, LastName, Email, Phone, University, Major, YearOfStudy)
VALUES
    ('Carlos',  'García',     'carlos.garcia@universidad.edu',   '+34612345678', 'Universidad Politécnica de Valencia', 'Ingeniería Informática', 3),
    ('María',   'López',      'maria.lopez@universidad.edu',     '+34623456789', 'Universidad de Valencia',             'Ciencia de Datos',       4),
    ('Juan',    'Martínez',   'juan.martinez@universidad.edu',   '+34634567890', 'Universidad Politécnica de Valencia', 'Ingeniería del Software', 2),
    ('Ana',     'Fernández',  'ana.fernandez@universidad.edu',   '+34645678901', 'Universidad de Valencia',             'Matemáticas',            3),
    ('Pedro',   'Sánchez',    'pedro.sanchez@universidad.edu',   '+34656789012', 'Universidad Jaume I',                 'Ingeniería Informática', 4),
    ('Laura',   'Rodríguez',  'laura.rodriguez@universidad.edu', '+34667890123', 'Universidad de Alicante',             'Inteligencia Artificial', 5),
    ('Miguel',  'Hernández',  'miguel.hernandez@universidad.edu','+34678901234', 'Universidad Politécnica de Valencia', 'Sistemas de Información', 3),
    ('Sofia',   'Ruiz',       'sofia.ruiz@universidad.edu',      '+34689012345', 'Universidad de Valencia',             'Ingeniería Informática', 2),
    ('David',   'Jiménez',    'david.jimenez@universidad.edu',   '+34690123456', 'Universidad Jaume I',                 'Ciberseguridad',         4),
    ('Elena',   'Moreno',     'elena.moreno@universidad.edu',    '+34601234567', 'Universidad de Alicante',             'Desarrollo Web',         3);

-- Mentores
INSERT INTO Mentors (FirstName, LastName, Email, Company, Specialization, AvailableSlots)
VALUES
    ('Roberto',   'Vega',    'roberto.vega@techcorp.com',       'TechCorp Solutions',   'Machine Learning',    5),
    ('Carmen',    'Navarro', 'carmen.navarro@datawise.io',      'DataWise Analytics',   'Data Engineering',    4),
    ('Francisco', 'Torres',  'francisco.torres@cloudnine.com',  'CloudNine Services',   'Cloud Architecture',  3),
    ('Isabel',    'Castro',  'isabel.castro@webdev.es',         'WebDev España',        'Frontend Development',5),
    ('Antonio',   'Ortega',  'antonio.ortega@securetech.com',   'SecureTech',           'Cybersecurity',       4);

-- Jueces
INSERT INTO Judges (FirstName, LastName, Email, Company, Expertise, YearsOfExperience)
VALUES
    ('Patricia', 'Vargas',  'patricia.vargas@innovation.com',  'Innovation Labs',         'Product Development', 12),
    ('Ricardo',  'Molina',  'ricardo.molina@venture.cap',      'Venture Capital Partners','Startup Evaluation',  15),
    ('Cristina', 'Delgado', 'cristina.delgado@techgiant.com',  'TechGiant Inc',           'Software Architecture',10),
    ('Fernando', 'Ramos',   'fernando.ramos@academia.edu',     'Academia Research',       'AI Research',          8),
    ('Beatriz',  'Serrano', 'beatriz.serrano@ux.design',       'UX Design Studio',        'User Experience',      9);

-- Equipos (mediante procedimiento, que también crea TeamStatistics y el primer miembro)
CALL sp_create_team_with_leader('Code Wizards',  1, 5);
CALL sp_create_team_with_leader('Data Dragons',  2, 4);
CALL sp_create_team_with_leader('Cloud Ninjas',  5, 5);

-- Miembros adicionales
INSERT INTO TeamMembers (TeamID, ParticipantID, Role) VALUES
    (1, 3, 'Developer'),
    (1, 4, 'Designer'),
    (1, 7, 'Developer'),
    (2, 6, 'Data Scientist'),
    (2, 8, 'Developer'),
    (3, 9, 'Security Expert'),
    (3, 10,'Frontend Developer');

-- Proyectos
INSERT INTO Projects (TeamID, ProjectName, Description, Category, TechnologyStack, GitHubURL, Status) VALUES
    (1, 'EcoTracker',   'Aplicación para monitorear y reducir la huella de carbono personal',
        'Sostenibilidad', 'React, Node.js, MongoDB, TensorFlow',
        'https://github.com/codewizards/ecotracker',  'Submitted'),
    (2, 'HealthPredict', 'Sistema de predicción de enfermedades usando IA',
        'Salud',          'Python, Scikit-learn, FastAPI, PostgreSQL',
        'https://github.com/datadragons/healthpredict','Submitted'),
    (3, 'SecureVault',  'Gestor de contraseñas descentralizado con blockchain',
        'Seguridad',      'Solidity, React, Web3.js, IPFS',
        'https://github.com/cloudninjas/securevault',  'In Progress');

-- Marcar fecha de envío para los proyectos enviados
UPDATE Projects SET SubmissionDate = NOW() WHERE Status = 'Submitted';

-- Sesiones de mentoría
INSERT INTO MentoringSessions (MentorID, TeamID, SessionDate, Duration, Topic, Notes) VALUES
    (1, 1, '2025-01-20 10:00:00', 60, 'Optimización del modelo ML',   'Discutimos técnicas de feature engineering'),
    (4, 1, '2025-01-22 15:00:00', 45, 'Mejoras de UI/UX',             'Revisión del diseño de la interfaz'),
    (2, 2, '2025-01-21 11:00:00', 90, 'Pipeline de datos',            'Configuración de ETL y limpieza de datos'),
    (1, 2, '2025-01-23 14:00:00', 60, 'Selección de algoritmos',      'Comparativa de modelos de clasificación'),
    (3, 3, '2025-01-22 09:00:00', 75, 'Arquitectura cloud',           'Diseño de infraestructura en AWS'),
    (5, 3, '2025-01-24 16:00:00', 60, 'Auditoría de seguridad',       'Revisión de vulnerabilidades del smart contract');

-- Evaluaciones (usando procedimiento)
CALL sp_evaluate_project(1, 1, 8.5, 9.0, 8.0, 8.5, 'Excelente concepto con buena ejecución técnica. La interfaz es intuitiva.');
CALL sp_evaluate_project(1, 2, 9.0, 8.5, 7.5, 9.0, 'Muy innovador. Potencial comercial significativo.');
CALL sp_evaluate_project(1, 3, 8.0, 9.5, 8.5, 8.0, 'Arquitectura sólida. Bien documentado.');
CALL sp_evaluate_project(2, 1, 9.5, 8.0, 8.0, 7.5, 'Innovación destacada en el sector salud.');
CALL sp_evaluate_project(2, 4, 9.0, 8.5, 9.0, 8.0, 'Modelo predictivo prometedor con buena precisión.');
CALL sp_evaluate_project(2, 5, 8.5, 8.0, 8.5, 9.0, 'Interfaz médica bien diseñada y accesible.');

-- Premios predefinidos (sin asignar aún)
INSERT INTO Awards (AwardName, Category, Prize, ProjectID, AwardedDate) VALUES
    ('Primer Lugar',              'General',           '5000€ + Incubación',        NULL, NULL),
    ('Mejor Innovación',          'Innovación',        '2000€ + Mentoría',          NULL, NULL),
    ('Mejor Diseño UX',           'Diseño',            '1500€ + Curso UX',          NULL, NULL),
    ('Premio del Público',        'Votación Popular',  '1000€',                     NULL, NULL),
    ('Mejor Proyecto Sostenible', 'Sostenibilidad',    '2000€ + Certificación Verde',NULL,NULL);

-- ============================================================
-- SECCIÓN 5b: USUARIO ADMINISTRADOR  ★ SISTEMA AUTH ★
-- ============================================================
-- Credenciales por defecto:
--   Usuario:    admin
--   Contraseña: Admin1234!
--
-- Hash generado con: password_hash('Admin1234!', PASSWORD_BCRYPT, ['cost' => 12])
-- ⚠️  CAMBIA LA CONTRASEÑA en tu primer login desde el CRM.
-- ⚠️  Para generar un hash propio ejecuta:
--       /c/xampp/php/php -r "echo password_hash('TuContraseña', PASSWORD_BCRYPT, ['cost' => 12]);"
-- ============================================================
INSERT INTO AdminUsers (Username, PasswordHash, Email, FullName, Role, IsActive)
VALUES (
    'admin',
    '$2y$12$kqFjkU2ASHEcKqCC/CaZqOWq0LdmNOC5dZdn.4eHTWEHpSACge2dS',
    'admin@hackathon.local',
    'Administrador del Sistema',
    'admin',
    1
);

-- ============================================================
-- SECCIÓN 6: RESTAURAR CONFIGURACIÓN DE SESIÓN
-- ============================================================
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;
SET SQL_MODE=@OLD_SQL_MODE;

-- ============================================================
-- SECCIÓN 7: VERIFICACIÓN
-- ============================================================
SELECT '══ TABLAS CREADAS ══' AS Info;
SHOW TABLES;

SELECT '══ TRIGGERS ══' AS Info;
SELECT TRIGGER_NAME, EVENT_MANIPULATION, EVENT_OBJECT_TABLE, ACTION_TIMING
FROM   information_schema.TRIGGERS
WHERE  TRIGGER_SCHEMA = 'HackathonDB'
ORDER BY EVENT_OBJECT_TABLE, ACTION_TIMING;

SELECT '══ RESUMEN DE DATOS ══' AS Info;
SELECT
    (SELECT COUNT(*) FROM HackathonParticipants) AS Participantes,
    (SELECT COUNT(*) FROM Teams)                 AS Equipos,
    (SELECT COUNT(*) FROM Mentors)               AS Mentores,
    (SELECT COUNT(*) FROM Judges)                AS Jueces,
    (SELECT COUNT(*) FROM Projects)              AS Proyectos,
    (SELECT COUNT(*) FROM Evaluations)           AS Evaluaciones,
    (SELECT COUNT(*) FROM MentoringSessions)     AS SesionesMentoria,
    (SELECT COUNT(*) FROM AdminUsers)            AS UsuariosAdmin,
    (SELECT COUNT(*) FROM EventLog)              AS EventosRegistrados,
    (SELECT COUNT(*) FROM AuditLog)              AS RegistrosAuditoria;

SELECT '══ RANKING DE PROYECTOS ══' AS Info;
SELECT * FROM vw_project_rankings;

SELECT '══ USUARIOS ADMIN ══' AS Info;
SELECT UserID, Username, Email, FullName, Role, IsActive, CreatedAt
FROM   vw_admin_users;

SELECT '══ EVENTOS RECIENTES ══' AS Info;
SELECT * FROM vw_recent_events LIMIT 10;
