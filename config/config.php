<?php
declare(strict_types=1);

// ═══════════════════════════════════════════════════════════════════════════════
// FUENTE ÚNICA DE VERDAD — Toda configuración de la aplicación vive aquí
// ═══════════════════════════════════════════════════════════════════════════════

// ─── Base de datos ─────────────────────────────────────────────────────────────
const DB_HOST    = '127.0.0.1';
const DB_PORT    = 3307;
const DB_NAME    = 'HackathonDB';
const DB_USER    = 'root';
const DB_PASS    = '';
const DB_CHARSET = 'utf8mb4';

// ─── Aplicación ────────────────────────────────────────────────────────────────
const APP_NAME    = 'Hackathon CRM';
const APP_VERSION = '1.0.0';
const BASE_URL    = 'http://localhost/hackathon-crm';

// ─── Paginación ────────────────────────────────────────────────────────────────
const RECORDS_PER_PAGE = 15;

// ─── Módulos disponibles (lista blanca para el router) ─────────────────────────
const VALID_MODULES = [
    'dashboard', 'teams', 'participants', 'projects',
    'mentors', 'judges', 'evaluations', 'awards',
];

// ─── Acciones disponibles (lista blanca para el router) ────────────────────────
const VALID_ACTIONS = [
    'index', 'show', 'create', 'store',
    'edit', 'update', 'delete', 'assign',
];

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
