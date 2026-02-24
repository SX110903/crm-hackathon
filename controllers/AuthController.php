<?php
declare(strict_types=1);

require_once ROOT_PATH . '/models/UserModel.php';

/**
 * AuthController — Gestiona el ciclo de autenticación (login / logout).
 *
 * Seguridad aplicada:
 *  - Protección CSRF con token de sesión.
 *  - Protección de fuerza bruta: bloqueo temporal tras N intentos fallidos.
 *  - session_regenerate_id() en login/logout (vía AuthGuard).
 *  - Cookies HttpOnly + SameSite configuradas en index.php.
 *  - No revela si el usuario existe o no (mensaje genérico).
 */
class AuthController extends BaseController
{
    private UserModel $userModel;
    private AuthGuard $auth;

    private const MAX_ATTEMPTS      = 5;
    private const LOCKOUT_SECONDS   = 300; // 5 minutos
    private const SESSION_ATTEMPTS  = '_crm_login_attempts';
    private const SESSION_LAST_FAIL = '_crm_login_last_fail';

    public function __construct()
    {
        parent::__construct();
        $this->userModel = new UserModel();
        $this->auth      = AuthGuard::getInstance();
    }

    // ─── GET: Formulario de login ────────────────────────────────────────────────
    public function login(?int $id): void
    {
        // Si ya está autenticado, redirigir al dashboard
        if ($this->auth->isAuthenticated()) {
            $this->redirect($this->url('dashboard'));
        }

        $csrfToken = $this->auth->csrfToken();
        $this->renderAuthView(compact('csrfToken'));
    }

    // ─── POST: Procesar credenciales ─────────────────────────────────────────────
    public function store(?int $id): void
    {
        if (!$this->isPost()) {
            $this->redirect($this->url('auth', 'login'));
        }

        // 1. Validar token CSRF
        if (!$this->auth->validateCsrf($this->post('_csrf_token'))) {
            $this->renderAuthView([
                'error'     => 'Token de seguridad inválido. Recarga la página e inténtalo de nuevo.',
                'csrfToken' => $this->auth->csrfToken(),
            ]);
            return;
        }

        // 2. Comprobar bloqueo por fuerza bruta
        if ($this->isLockedOut()) {
            $remaining = $this->lockoutSecondsRemaining();
            $this->renderAuthView([
                'error'     => "Demasiados intentos fallidos. Espera {$remaining} segundos.",
                'csrfToken' => $this->auth->csrfToken(),
            ]);
            return;
        }

        $username = $this->post('username');
        $password = $this->post('password');

        // 3. Buscar usuario y verificar contraseña
        $user = $this->userModel->findByUsername($username);

        if (!$user || !$this->userModel->verifyPassword($password, $user['PasswordHash'])) {
            $this->recordFailedAttempt();
            $attemptsLeft = self::MAX_ATTEMPTS - $this->failedAttemptCount();
            $this->renderAuthView([
                'error'     => 'Credenciales incorrectas. ' . ($attemptsLeft > 0
                    ? "Intentos restantes: {$attemptsLeft}."
                    : 'Cuenta bloqueada temporalmente.'),
                'csrfToken' => $this->auth->csrfToken(),
                'username'  => htmlspecialchars($username, ENT_QUOTES, 'UTF-8'),
            ]);
            return;
        }

        // 4. Login exitoso
        $this->clearFailedAttempts();
        $this->userModel->updateLastLogin((int) $user['UserID']);
        $this->auth->login($user);

        $this->redirect($this->url('dashboard'));
    }

    // ─── POST: Cerrar sesión ─────────────────────────────────────────────────────
    public function logout(?int $id): void
    {
        if ($this->isPost() && $this->auth->validateCsrf($this->post('_csrf_token'))) {
            $this->auth->logout();
        }
        $this->redirect($this->url('auth', 'login'));
    }

    // ─── Brute-force helpers ─────────────────────────────────────────────────────

    private function isLockedOut(): bool
    {
        if ($this->failedAttemptCount() < self::MAX_ATTEMPTS) {
            return false;
        }

        $elapsed = time() - (int) ($_SESSION[self::SESSION_LAST_FAIL] ?? 0);
        if ($elapsed >= self::LOCKOUT_SECONDS) {
            $this->clearFailedAttempts();
            return false;
        }

        return true;
    }

    private function lockoutSecondsRemaining(): int
    {
        $elapsed = time() - (int) ($_SESSION[self::SESSION_LAST_FAIL] ?? 0);
        return max(0, self::LOCKOUT_SECONDS - $elapsed);
    }

    private function failedAttemptCount(): int
    {
        return (int) ($_SESSION[self::SESSION_ATTEMPTS] ?? 0);
    }

    private function recordFailedAttempt(): void
    {
        $_SESSION[self::SESSION_ATTEMPTS]  = $this->failedAttemptCount() + 1;
        $_SESSION[self::SESSION_LAST_FAIL] = time();
    }

    private function clearFailedAttempts(): void
    {
        unset($_SESSION[self::SESSION_ATTEMPTS], $_SESSION[self::SESSION_LAST_FAIL]);
    }

    // ─── Renderizado propio (sin layout de la app) ───────────────────────────────

    private function renderAuthView(array $data = []): never
    {
        extract($data, EXTR_SKIP);
        require ROOT_PATH . '/views/auth/login.php';
        exit;
    }
}
