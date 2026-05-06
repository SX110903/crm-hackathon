<?php
declare(strict_types=1);

/**
 * Setup de base de datos — Crea la tabla AdminUsers e inserta el usuario admin.
 *
 * USO: Ejecuta este script UNA SOLA VEZ desde la línea de comandos o navegador:
 *   php database/setup.php
 *   http://localhost/hackathon-crm/database/setup.php
 *
 * ¡ELIMINA O PROTEGE ESTE FICHERO DESPUÉS DE EJECUTARLO EN PRODUCCIÓN!
 */

// ─── Bootstrap mínimo ────────────────────────────────────────────────────────
define('ROOT_PATH', dirname(__DIR__));
require_once ROOT_PATH . '/core/EnvLoader.php';
EnvLoader::getInstance(ROOT_PATH . '/.env');
require_once ROOT_PATH . '/config/config.php';
require_once ROOT_PATH . '/core/Database.php';

$db = Database::getInstance();

$log = [];
$ok  = true;

// ─── 1. Crear tabla AdminUsers ────────────────────────────────────────────────
try {
    $db->execute("
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
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");
    $log[] = ['ok', 'Tabla AdminUsers creada (o ya existía).'];
} catch (\PDOException $e) {
    $log[] = ['error', 'Error al crear AdminUsers: ' . $e->getMessage()];
    $ok = false;
}

// ─── 2. Crear vista TeamStatistics (opcional) ─────────────────────────────────
try {
    $db->execute("
        CREATE OR REPLACE VIEW TeamStatistics AS
        SELECT
            t.TeamID,
            COUNT(DISTINCT tm.MemberID)         AS TotalMembers,
            COALESCE(SUM(ms.Duration) / 60, 0)  AS TotalMentoringHours,
            COUNT(DISTINCT ms.SessionID)         AS TotalMentoringSessions,
            AVG(e.TotalScore)                    AS AverageProjectScore
        FROM Teams t
        LEFT JOIN TeamMembers      tm ON t.TeamID = tm.TeamID
        LEFT JOIN MentoringSessions ms ON t.TeamID = ms.TeamID
        LEFT JOIN Projects          p  ON t.TeamID = p.TeamID
        LEFT JOIN Evaluations       e  ON p.ProjectID = e.ProjectID
        GROUP BY t.TeamID
    ");
    $log[] = ['ok', 'Vista TeamStatistics creada/actualizada.'];
} catch (\PDOException $e) {
    $log[] = ['warn', 'Vista TeamStatistics no creada (puede ignorarse): ' . $e->getMessage()];
}

// ─── 3. Insertar usuario administrador ────────────────────────────────────────
// Credenciales por defecto: admin / Admin1234!
// CAMBIA LA CONTRASEÑA inmediatamente después de hacer login.
$adminUsername = 'admin';
$adminPassword = 'Admin1234!';
$adminEmail    = 'admin@hackathon.local';
$adminFullName = 'Administrador del Sistema';

$existing = $db->fetchOne(
    'SELECT UserID FROM AdminUsers WHERE Username = ?',
    [$adminUsername]
);

if ($existing) {
    $log[] = ['warn', "El usuario '{$adminUsername}' ya existe. No se ha modificado."];
} else {
    try {
        $hash = password_hash($adminPassword, PASSWORD_BCRYPT, ['cost' => 12]);
        $db->execute(
            'INSERT INTO AdminUsers (Username, PasswordHash, Email, FullName, Role)
             VALUES (?, ?, ?, ?, ?)',
            [$adminUsername, $hash, $adminEmail, $adminFullName, 'admin']
        );
        $log[] = ['ok', "Usuario administrador creado: <strong>{$adminUsername}</strong> / {$adminPassword}"];
        $log[] = ['warn', '⚠️  Cambia la contraseña inmediatamente después de tu primer login.'];
    } catch (\PDOException $e) {
        $log[] = ['error', 'Error al crear el usuario admin: ' . $e->getMessage()];
        $ok = false;
    }
}

// ─── Output ──────────────────────────────────────────────────────────────────
header('Content-Type: text/html; charset=UTF-8');
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Setup — <?= APP_NAME ?></title>
  <style>
    body { font-family: sans-serif; background: #f1f5f9; padding: 2rem; }
    .box { background: #fff; border-radius: 8px; padding: 2rem; max-width: 640px; margin: 0 auto; box-shadow: 0 2px 8px rgba(0,0,0,.1); }
    h1 { font-size: 1.2rem; margin-bottom: 1rem; }
    .ok    { color: #059669; }
    .error { color: #dc2626; }
    .warn  { color: #d97706; }
    li { margin: .4rem 0; font-size: .9rem; }
    .done { margin-top: 1.5rem; padding: 1rem; background: #f0fdf4; border: 1px solid #bbf7d0; border-radius: 6px; }
    a { color: #2563eb; }
  </style>
</head>
<body>
<div class="box">
  <h1>Setup — <?= htmlspecialchars(APP_NAME, ENT_QUOTES, 'UTF-8') ?></h1>
  <ul>
    <?php foreach ($log as [$type, $msg]): ?>
    <li class="<?= $type ?>">
      <?= $type === 'ok' ? '✓' : ($type === 'warn' ? '⚠' : '✗') ?>
      <?= $msg ?>
    </li>
    <?php endforeach; ?>
  </ul>

  <?php if ($ok): ?>
  <div class="done">
    <strong>✓ Setup completado.</strong><br>
    <a href="<?= BASE_URL ?>/?module=auth&action=login">→ Ir al login</a>
    <br><br>
    <strong style="color:#dc2626;">⚠️ IMPORTANTE:</strong> elimina o protege el fichero
    <code>database/setup.php</code> antes de desplegar en producción.
  </div>
  <?php endif; ?>
</div>
</body>
</html>
