<?php
declare(strict_types=1);

/**
 * AuthGuard — Singleton que gestiona la autenticación y tokens CSRF.
 *
 * Responsabilidades:
 *  - Mantener el estado de sesión del usuario autenticado.
 *  - Regenerar ID de sesión en login/logout para prevenir fixation.
 *  - Generar y validar tokens CSRF con comparación en tiempo constante.
 *  - Proveer un guard (requireAuth) que redirige al login si no hay sesión.
 */
final class AuthGuard
{
    private static ?self $instance = null;

    private const SESSION_USER_KEY = '_crm_auth_user';
    private const SESSION_CSRF_KEY = '_crm_csrf_token';

    private function __construct() {}

    // ─── Punto de acceso global ──────────────────────────────────────────────────
    public static function getInstance(): self
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    // ─── Estado de autenticación ─────────────────────────────────────────────────

    public function isAuthenticated(): bool
    {
        return isset($_SESSION[self::SESSION_USER_KEY]);
    }

    /** Establece la sesión de usuario tras login exitoso. */
    public function login(array $user): void
    {
        // Regenerar ID para prevenir session fixation
        session_regenerate_id(true);

        $_SESSION[self::SESSION_USER_KEY] = [
            'id'        => (int) $user['UserID'],
            'username'  => $user['Username'],
            'fullName'  => $user['FullName'],
            'role'      => $user['Role'],
            'loginTime' => time(),
        ];
    }

    /** Destruye la sesión del usuario. */
    public function logout(): void
    {
        unset($_SESSION[self::SESSION_USER_KEY], $_SESSION[self::SESSION_CSRF_KEY]);
        session_regenerate_id(true);
    }

    /** Devuelve los datos del usuario autenticado, o null si no hay sesión. */
    public function currentUser(): ?array
    {
        return $_SESSION[self::SESSION_USER_KEY] ?? null;
    }

    // ─── Guard ───────────────────────────────────────────────────────────────────

    /**
     * Redirige al login si no hay sesión activa.
     * Debe llamarse antes de mostrar cualquier vista protegida.
     */
    public function requireAuth(string $loginUrl): void
    {
        if (!$this->isAuthenticated()) {
            header('Location: ' . $loginUrl);
            exit;
        }
    }

    // ─── CSRF ────────────────────────────────────────────────────────────────────

    /**
     * Genera (o recupera) el token CSRF de la sesión actual.
     * Un solo token por sesión, regenerado en cada login.
     */
    public function csrfToken(): string
    {
        if (!isset($_SESSION[self::SESSION_CSRF_KEY])) {
            $_SESSION[self::SESSION_CSRF_KEY] = bin2hex(random_bytes(32));
        }
        return $_SESSION[self::SESSION_CSRF_KEY];
    }

    /**
     * Valida un token CSRF usando comparación en tiempo constante
     * para prevenir timing attacks.
     */
    public function validateCsrf(string $submittedToken): bool
    {
        $storedToken = $_SESSION[self::SESSION_CSRF_KEY] ?? '';
        return $storedToken !== '' && hash_equals($storedToken, $submittedToken);
    }

    // ─── Singleton guards ────────────────────────────────────────────────────────
    private function __clone() {}

    public function __wakeup(): never
    {
        throw new \LogicException('AuthGuard Singleton no puede deserializarse.');
    }
}
