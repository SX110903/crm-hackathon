<?php
declare(strict_types=1);

/**
 * BaseController — Clase abstracta base para todos los controladores.
 *
 * Centraliza:
 *  - Renderizado de vistas con layout
 *  - Redirección y mensajes flash (sesión)
 *  - Acceso tipado y seguro a datos POST
 *  - Construcción de URLs internas
 *  - Verificación de método HTTP (POST / PUT override)
 *  - Escape de output para prevenir XSS
 *  - Paginación
 */
abstract class BaseController
{
    protected Database $db;

    public function __construct()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $this->db = Database::getInstance();
    }

    // ═══════════════════════════════════════════════════════════════════════════
    // RENDERIZADO DE VISTAS
    // ═══════════════════════════════════════════════════════════════════════════

    /**
     * Renderiza una vista dentro del layout completo.
     * Expone las variables del array $data en el scope de la vista.
     */
    protected function render(string $view, array $data = []): void
    {
        $data['flash']       = $this->consumeFlash();
        $data['currentPage'] = explode('/', $view)[0];

        extract($data, EXTR_SKIP);

        require_once ROOT_PATH . '/views/layout/header.php';
        require_once ROOT_PATH . '/views/layout/sidebar.php';

        echo '<main class="main-content">';

        $viewFile = ROOT_PATH . "/views/{$view}.php";
        if (file_exists($viewFile)) {
            require $viewFile;
        } else {
            echo '<div class="alert alert-error">Vista no encontrada: '
                . htmlspecialchars($view, ENT_QUOTES, 'UTF-8') . '</div>';
        }

        echo '</main>';

        require_once ROOT_PATH . '/views/layout/footer.php';
    }

    // ═══════════════════════════════════════════════════════════════════════════
    // REDIRECCIÓN
    // ═══════════════════════════════════════════════════════════════════════════

    protected function redirect(string $url): never
    {
        header('Location: ' . $url);
        exit;
    }

    // ═══════════════════════════════════════════════════════════════════════════
    // MENSAJES FLASH (sesión)
    // ═══════════════════════════════════════════════════════════════════════════

    protected function setFlash(string $type, string $message): void
    {
        $_SESSION['flash'] = ['type' => $type, 'message' => $message];
    }

    /** Consume el flash (lo lee y lo borra de la sesión). */
    private function consumeFlash(): array
    {
        $flash = $_SESSION['flash'] ?? [];
        unset($_SESSION['flash']);
        return $flash;
    }

    // ═══════════════════════════════════════════════════════════════════════════
    // ACCESO TIPADO A POST — Evita acceso directo a $_POST
    // ═══════════════════════════════════════════════════════════════════════════

    /** Lee un campo POST como string limpio (trim). */
    protected function post(string $key, string $default = ''): string
    {
        return isset($_POST[$key]) ? trim((string) $_POST[$key]) : $default;
    }

    /** Lee un campo POST como entero. */
    protected function postInt(string $key, int $default = 0): int
    {
        return isset($_POST[$key]) ? (int) $_POST[$key] : $default;
    }

    /** Lee un campo POST como flotante. */
    protected function postFloat(string $key, float $default = 0.0): float
    {
        return isset($_POST[$key]) ? (float) $_POST[$key] : $default;
    }

    /** Lee un campo POST como fecha (Y-m-d). Devuelve null si no es válida. */
    protected function postDate(string $key): ?string
    {
        $raw = $this->post($key);
        if ($raw === '') return null;
        $date = \DateTime::createFromFormat('Y-m-d', $raw);
        return ($date && $date->format('Y-m-d') === $raw) ? $raw : null;
    }

    // ═══════════════════════════════════════════════════════════════════════════
    // VERIFICACIÓN DE MÉTODO HTTP
    // ═══════════════════════════════════════════════════════════════════════════

    protected function isPost(): bool
    {
        return $_SERVER['REQUEST_METHOD'] === 'POST';
    }

    /**
     * Verifica que la petición sea POST con _method=PUT.
     * HTML forms solo soportan GET/POST; usamos override para PUT.
     */
    protected function isPut(): bool
    {
        return $_SERVER['REQUEST_METHOD'] === 'POST'
            && ($this->post('_method') === 'PUT');
    }

    // ═══════════════════════════════════════════════════════════════════════════
    // CONSTRUCCIÓN DE URLs INTERNAS
    // ═══════════════════════════════════════════════════════════════════════════

    protected function url(string $module, string $action = 'index', ?int $id = null): string
    {
        $url = BASE_URL . "/?module={$module}&action={$action}";
        return $id !== null ? $url . "&id={$id}" : $url;
    }

    // ═══════════════════════════════════════════════════════════════════════════
    // ESCAPE DE OUTPUT — Previene XSS en todas las vistas es una variable Protected
    // ═══════════════════════════════════════════════════════════════════════════

    protected function e(mixed $value): string
    {
        return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
    }

    // ═══════════════════════════════════════════════════════════════════════════
    // PAGINACIÓN
    // ═══════════════════════════════════════════════════════════════════════════

    protected function currentPage(): int
    {
        return max(1, (int) ($_GET['page'] ?? 1));
    }

    protected function buildPagination(int $totalRecords, int $currentPage): array
    {
        $totalPages = max(1, (int) ceil($totalRecords / RECORDS_PER_PAGE));
        return [
            'currentPage'  => max(1, min($currentPage, $totalPages)),
            'totalPages'   => $totalPages,
            'totalRecords' => $totalRecords,
            'perPage'      => RECORDS_PER_PAGE,
        ];
    }

    // ═══════════════════════════════════════════════════════════════════════════
    // GUARDS — Abortan la acción si no se cumple la condición
    // ═══════════════════════════════════════════════════════════════════════════

    /** Redirige si el método no es POST. */
    protected function requirePost(string $fallbackUrl): void
    {
        if (!$this->isPost()) {
            $this->redirect($fallbackUrl);
        }
    }

    /** Redirige si el método no es POST+PUT. */
    protected function requirePut(string $fallbackUrl): void
    {
        if (!$this->isPut()) {
            $this->redirect($fallbackUrl);
        }
    }

    /** Redirige si $id es null. */
    protected function requireId(?int $id, string $fallbackUrl): void
    {
        if ($id === null || $id <= 0) {
            $this->redirect($fallbackUrl);
        }
    }
}
