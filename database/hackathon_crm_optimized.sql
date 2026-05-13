-- ============================================
-- hackathon_crm — Optimizacion a medida
-- Indexes, Constraints, Triggers, Stored Procedures, Views
-- MySQL 8.4 — ejecutar como root para triggers
-- ============================================

USE hackathon_crm;

-- ============================================
-- 1. INDEXES
-- ============================================

CREATE INDEX idx_teams_category           ON teams(category);
CREATE INDEX idx_teams_category_event     ON teams(category, event_id);

CREATE INDEX idx_projects_status          ON projects(status);
CREATE INDEX idx_projects_team_status     ON projects(team_id, status);

CREATE INDEX idx_evaluations_project      ON evaluations(project_id);
CREATE INDEX idx_evaluations_judge        ON evaluations(judge_id);
CREATE INDEX idx_evaluations_created      ON evaluations(created_at);

CREATE INDEX idx_events_status            ON events(status);
CREATE INDEX idx_events_dates             ON events(start_date, end_date);

CREATE INDEX idx_participants_university  ON participants(university);

CREATE INDEX idx_team_members_participant ON team_members(participant_id);

-- ============================================
-- 2. CONSTRAINTS
-- ============================================

-- Unique: un juez solo puede evaluar un proyecto una vez
ALTER TABLE evaluations
  ADD CONSTRAINT uq_evaluation_project_judge UNIQUE (project_id, judge_id);

-- Unique: un participante solo puede estar una vez en el mismo equipo
ALTER TABLE team_members
  ADD CONSTRAINT uq_team_member UNIQUE (team_id, participant_id);

-- CHECK: scores entre 0 y 10
ALTER TABLE evaluations
  ADD CONSTRAINT chk_innovation_score   CHECK (innovation_score   >= 0 AND innovation_score   <= 10),
  ADD CONSTRAINT chk_technical_score    CHECK (technical_score    >= 0 AND technical_score    <= 10),
  ADD CONSTRAINT chk_presentation_score CHECK (presentation_score >= 0 AND presentation_score <= 10),
  ADD CONSTRAINT chk_usability_score    CHECK (usability_score    >= 0 AND usability_score    <= 10);

-- CHECK: max_members positivo y razonable
ALTER TABLE teams
  ADD CONSTRAINT chk_max_members CHECK (max_members > 0 AND max_members <= 20);

-- CHECK: fecha fin no puede ser antes que fecha inicio
ALTER TABLE events
  ADD CONSTRAINT chk_event_dates CHECK (end_date >= start_date);

-- ============================================
-- 3. TRIGGERS
-- NOTE: Requires root + log_bin_trust_function_creators=1
--   SET GLOBAL log_bin_trust_function_creators = 1;
-- ============================================

DROP TRIGGER IF EXISTS trg_check_team_capacity;
DROP TRIGGER IF EXISTS trg_auto_evaluate_project;
DROP TRIGGER IF EXISTS trg_award_project_status;
DROP TRIGGER IF EXISTS trg_update_last_login;

DELIMITER $$

-- Trigger 1: impedir añadir miembros si el equipo está lleno
CREATE TRIGGER trg_check_team_capacity
BEFORE INSERT ON team_members
FOR EACH ROW
BEGIN
  DECLARE current_count INT;
  DECLARE max_allowed   TINYINT;
  SELECT COUNT(*)    INTO current_count FROM team_members WHERE team_id = NEW.team_id;
  SELECT max_members INTO max_allowed   FROM teams         WHERE id      = NEW.team_id;
  IF current_count >= max_allowed THEN
    SIGNAL SQLSTATE '45000'
      SET MESSAGE_TEXT = 'Team is already at maximum capacity';
  END IF;
END$$

-- Trigger 2: auto-cambiar status del proyecto a 'Evaluated'
-- cuando todos los jueces han enviado su evaluacion
CREATE TRIGGER trg_auto_evaluate_project
AFTER INSERT ON evaluations
FOR EACH ROW
BEGIN
  DECLARE total_judges INT;
  DECLARE eval_count   INT;
  SELECT COUNT(*) INTO total_judges FROM judges;
  SELECT COUNT(*) INTO eval_count FROM evaluations WHERE project_id = NEW.project_id;
  IF total_judges > 0 AND eval_count >= total_judges THEN
    UPDATE projects SET status = 'Evaluated'
     WHERE id = NEW.project_id AND status NOT IN ('Evaluated', 'Awarded');
  END IF;
END$$

-- Trigger 3: auto-cambiar status del proyecto a 'Awarded'
-- cuando se le asigna un premio
CREATE TRIGGER trg_award_project_status
AFTER UPDATE ON awards
FOR EACH ROW
BEGIN
  IF NEW.project_id IS NOT NULL AND (OLD.project_id IS NULL OR OLD.project_id != NEW.project_id) THEN
    UPDATE projects SET status = 'Awarded' WHERE id = NEW.project_id;
  END IF;
END$$

-- Trigger 4: registrar last_login en users al crear un token Sanctum
CREATE TRIGGER trg_update_last_login
AFTER INSERT ON personal_access_tokens
FOR EACH ROW
BEGIN
  IF NEW.tokenable_type = 'App\\Models\\User' THEN
    UPDATE users SET last_login = NOW() WHERE id = NEW.tokenable_id;
  END IF;
END$$

DELIMITER ;

-- ============================================
-- 4. STORED PROCEDURES
-- ============================================

DROP PROCEDURE IF EXISTS sp_get_leaderboard;
DROP PROCEDURE IF EXISTS sp_get_battle_scores;
DROP PROCEDURE IF EXISTS sp_get_event_stats;
DROP PROCEDURE IF EXISTS sp_get_judge_summary;

DELIMITER $$

-- SP 1: Leaderboard completo — usado en Dashboard y Awards
CREATE PROCEDURE sp_get_leaderboard(IN p_event_id INT)
BEGIN
  SELECT
    p.id AS project_id, p.name AS project_name, p.status, p.github_url, p.demo_url,
    t.name AS team_name, t.category AS team_category,
    COUNT(e.id) AS eval_count,
    ROUND(AVG(e.innovation_score),   2) AS avg_innovation,
    ROUND(AVG(e.technical_score),    2) AS avg_technical,
    ROUND(AVG(e.presentation_score), 2) AS avg_presentation,
    ROUND(AVG(e.usability_score),    2) AS avg_usability,
    ROUND(AVG((e.innovation_score + e.technical_score + e.presentation_score + e.usability_score) / 4), 2) AS total_score
  FROM projects p
  JOIN teams t ON p.team_id = t.id
  LEFT JOIN evaluations e ON p.id = e.project_id
  WHERE (p_event_id IS NULL OR t.event_id = p_event_id)
  GROUP BY p.id, p.name, p.status, p.github_url, p.demo_url, t.name, t.category
  ORDER BY total_score DESC;
END$$

-- SP 2: Marcador Red vs Blue — usado en Live Battle
CREATE PROCEDURE sp_get_battle_scores(IN p_event_id INT)
BEGIN
  SELECT
    t.category,
    COUNT(DISTINCT t.id) AS team_count,
    COUNT(DISTINCT p.id) AS project_count,
    COUNT(e.id) AS eval_count,
    ROUND(AVG((e.innovation_score + e.technical_score + e.presentation_score + e.usability_score) / 4), 2) AS avg_score,
    ROUND(SUM((e.innovation_score + e.technical_score + e.presentation_score + e.usability_score) / 4), 2) AS total_score
  FROM teams t
  LEFT JOIN projects p ON t.id = p.team_id
  LEFT JOIN evaluations e ON p.id = e.project_id
  WHERE t.category IN ('red', 'blue')
    AND (p_event_id IS NULL OR t.event_id = p_event_id)
  GROUP BY t.category;
END$$

-- SP 3: Stats de un evento — usado en Dashboard
CREATE PROCEDURE sp_get_event_stats(IN p_event_id INT)
BEGIN
  SELECT
    (SELECT COUNT(*) FROM teams WHERE event_id = p_event_id) AS teams,
    (SELECT COUNT(*) FROM team_members tm JOIN teams t ON tm.team_id = t.id WHERE t.event_id = p_event_id) AS participants,
    (SELECT COUNT(*) FROM projects p JOIN teams t ON p.team_id = t.id WHERE t.event_id = p_event_id) AS projects,
    (SELECT COUNT(*) FROM evaluations e JOIN projects p ON e.project_id = p.id JOIN teams t ON p.team_id = t.id WHERE t.event_id = p_event_id) AS evaluations,
    (SELECT COUNT(*) FROM teams WHERE event_id = p_event_id AND category = 'red')  AS red_teams,
    (SELECT COUNT(*) FROM teams WHERE event_id = p_event_id AND category = 'blue') AS blue_teams,
    (SELECT COUNT(*) FROM awards a JOIN projects p ON a.project_id = p.id JOIN teams t ON p.team_id = t.id WHERE t.event_id = p_event_id AND a.project_id IS NOT NULL) AS awards_assigned;
END$$

-- SP 4: Resumen de jueces — usado en Judges page
CREATE PROCEDURE sp_get_judge_summary()
BEGIN
  SELECT
    j.id,
    CONCAT(j.first_name, ' ', j.last_name) AS judge_name,
    j.company, j.expertise, j.years_of_experience,
    COUNT(e.id) AS evaluations_done,
    ROUND(AVG((e.innovation_score + e.technical_score + e.presentation_score + e.usability_score) / 4), 2) AS avg_score_given
  FROM judges j
  LEFT JOIN evaluations e ON j.id = e.judge_id
  GROUP BY j.id, j.first_name, j.last_name, j.company, j.expertise, j.years_of_experience
  ORDER BY evaluations_done DESC;
END$$

DELIMITER ;

-- ============================================
-- 5. VIEWS
-- ============================================

-- View: puntuaciones de proyectos — usada por Dashboard y Leaderboard
CREATE OR REPLACE VIEW v_project_scores AS
SELECT
  p.id, p.name AS project_name, p.status, p.technology_stack, p.github_url, p.demo_url,
  t.id AS team_id, t.name AS team_name, t.category AS team_category, t.event_id,
  COUNT(e.id) AS eval_count,
  ROUND(AVG(e.innovation_score),   2) AS avg_innovation,
  ROUND(AVG(e.technical_score),    2) AS avg_technical,
  ROUND(AVG(e.presentation_score), 2) AS avg_presentation,
  ROUND(AVG(e.usability_score),    2) AS avg_usability,
  ROUND(AVG((e.innovation_score + e.technical_score + e.presentation_score + e.usability_score) / 4), 2) AS avg_score
FROM projects p
JOIN teams t ON p.team_id = t.id
LEFT JOIN evaluations e ON p.id = e.project_id
GROUP BY p.id, p.name, p.status, p.technology_stack, p.github_url, p.demo_url,
         t.id, t.name, t.category, t.event_id;

-- View: puntuaciones por equipo — usada por Red/Blue panels
CREATE OR REPLACE VIEW v_team_scores AS
SELECT
  t.id, t.name, t.category, t.event_id, t.max_members,
  COUNT(DISTINCT tm.participant_id) AS member_count,
  COUNT(DISTINCT p.id) AS project_count,
  ROUND(AVG((e.innovation_score + e.technical_score + e.presentation_score + e.usability_score) / 4), 2) AS avg_score,
  ROUND(SUM((e.innovation_score + e.technical_score + e.presentation_score + e.usability_score) / 4), 2) AS total_score
FROM teams t
LEFT JOIN team_members tm ON t.id = tm.team_id
LEFT JOIN projects p ON t.id = p.team_id
LEFT JOIN evaluations e ON p.id = e.project_id
GROUP BY t.id, t.name, t.category, t.event_id, t.max_members;
