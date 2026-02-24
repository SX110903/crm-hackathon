<?php
declare(strict_types=1);

// ═══════════════════════════════════════════════════════════════════════════════
// FUENTE ÚNICA DE VERDAD — Toda configuración lee desde el fichero .env
// EnvLoader ya debe estar cargado antes de requerir este fichero.
// ═══════════════════════════════════════════════════════════════════════════════

// ─── Base de datos ─────────────────────────────────────────────────────────────
define('DB_HOST',    env('DB_HOST',    '127.0.0.1'));
define('DB_PORT',    envInt('DB_PORT', 3306));
define('DB_NAME',    env('DB_NAME',    'HackathonDB'));
define('DB_USER',    env('DB_USER',    'root'));
define('DB_PASS',    env('DB_PASS',    ''));
define('DB_CHARSET', env('DB_CHARSET', 'utf8mb4'));

// ─── Aplicación ────────────────────────────────────────────────────────────────
define('APP_NAME',    env('APP_NAME',    'Hackathon CRM'));
define('APP_VERSION', env('APP_VERSION', '1.0.0'));
define('APP_ENV',     env('APP_ENV',     'production'));
define('BASE_URL',    env('BASE_URL',    'http://localhost/hackathon-crm'));
define('APP_SECRET',  env('APP_SECRET',  'default_secret_change_me'));

// ─── Sesión ────────────────────────────────────────────────────────────────────
define('SESSION_LIFETIME', envInt('SESSION_LIFETIME', 7200));

// ─── Paginación ────────────────────────────────────────────────────────────────
define('RECORDS_PER_PAGE', envInt('RECORDS_PER_PAGE', 15));

// ─── Módulos disponibles (lista blanca para el router) ─────────────────────────
const VALID_MODULES = [
    'dashboard', 'teams', 'participants', 'projects',
    'mentors', 'judges', 'evaluations', 'awards',
    'auth',
];

// ─── Acciones disponibles (lista blanca para el router) ────────────────────────
const VALID_ACTIONS = [
    'index', 'show', 'create', 'store',
    'edit', 'update', 'delete', 'assign',
    'login', 'logout',
];

// ─── Módulos que NO requieren autenticación ────────────────────────────────────
const PUBLIC_MODULES = ['auth'];

// ─── Estados de proyecto ────────────────────────────────────────────────────────
const PROJECT_STATUSES = [
    'In Progress', 'Submitted', 'Under Review', 'Awarded', 'Rejected',
];

// ─── Categorías de proyecto ─────────────────────────────────────────────────────
const PROJECT_CATEGORIES = [
    'Sostenibilidad', 'Salud', 'Seguridad', 'Educación',
    'Fintech', 'Movilidad', 'IA', 'Web3', 'Otro',
];

// ─── Roles de miembro ───────────────────────────────────────────────────────────
const MEMBER_ROLES = [
    'Leader', 'Developer', 'Designer', 'Data Scientist',
    'Security Expert', 'Frontend Developer', 'Backend Developer', 'Member',
];

// ─── Tipos de evento del log ────────────────────────────────────────────────────
const EVENT_TYPES = [
    'TEAM_CREATED', 'MEMBER_JOINED', 'PROJECT_SUBMITTED',
    'PROJECT_EVALUATED', 'AWARD_GRANTED',
];
