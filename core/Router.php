<?php
declare(strict_types=1);

/**
 * Router — Singleton que despacha cada petición al controlador correcto.
 *
 * Seguridad:
 *  - Lista blanca de módulos y acciones (config.php).
 *  - AuthGuard aplicado a todos los módulos excepto PUBLIC_MODULES.
 *  - Carga de clases lazy (solo cuando se necesitan).
 */
final class Router
{
    private static ?self $instance = null;

    private function __construct() {}

    public static function getInstance(): self
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    // ═══════════════════════════════════════════════════════════════════════════
    // DESPACHO PRINCIPAL
    // ═══════════════════════════════════════════════════════════════════════════

    public function dispatch(): void
    {
        $module = $this->sanitizeSegment($_GET['module'] ?? 'dashboard');
        $action = $this->sanitizeSegment($_GET['action'] ?? 'index');
        $id     = isset($_GET['id']) ? (int) $_GET['id'] : null;

        // Validar contra la lista blanca (fuente única de verdad en config.php)
        if (!in_array($module, VALID_MODULES, true)) {
            $module = 'dashboard';
        }
        if (!in_array($action, VALID_ACTIONS, true)) {
            $action = 'index';
        }

        // ── AuthGuard: proteger todos los módulos excepto los públicos ──────────
        if (!in_array($module, PUBLIC_MODULES, true)) {
            AuthGuard::getInstance()->requireAuth(
                BASE_URL . '/?module=auth&action=login'
            );
        }

        $controllerClass = ucfirst($module) . 'Controller';

        $this->loadClass('BaseModel',      ROOT_PATH . '/core/BaseModel.php');
        $this->loadClass('BaseController', ROOT_PATH . '/core/BaseController.php');

        $controllerFile = ROOT_PATH . "/controllers/{$controllerClass}.php";
        if (!file_exists($controllerFile)) {
            $this->render404("Controlador '{$controllerClass}' no encontrado.");
            return;
        }

        $this->loadClass($controllerClass, $controllerFile);

        if (!class_exists($controllerClass)) {
            $this->render404("Clase '{$controllerClass}' no definida.");
            return;
        }

        $controller = new $controllerClass();

        if (!method_exists($controller, $action)) {
            $this->render404("Acción '{$action}' no existe en '{$controllerClass}'.");
            return;
        }

        $controller->$action($id);
    }

    // ═══════════════════════════════════════════════════════════════════════════
    // HELPERS PRIVADOS
    // ═══════════════════════════════════════════════════════════════════════════

    private function sanitizeSegment(string $value): string
    {
        return preg_replace('/[^a-zA-Z0-9_]/', '', trim($value));
    }

    private function loadClass(string $className, string $filePath): void
    {
        if (!class_exists($className, false) && file_exists($filePath)) {
            require_once $filePath;
        }
    }

    private function render404(string $detail = ''): void
    {
        http_response_code(404);
        echo '<!DOCTYPE html><html lang="es"><head><meta charset="UTF-8">'
            . '<title>404 — ' . APP_NAME . '</title></head><body>'
            . '<h1 style="font-family:sans-serif;color:#ef4444">404 — Página no encontrada</h1>';
        if ($detail && APP_ENV === 'development') {
            echo '<p style="font-family:sans-serif;color:#64748b">'
                . htmlspecialchars($detail, ENT_QUOTES, 'UTF-8') . '</p>';
        }
        echo '<p><a href="' . BASE_URL . '" style="font-family:sans-serif">← Volver al inicio</a></p>'
            . '</body></html>';
    }

    // ─── Singleton guards ───────────────────────────────────────────────────────
    private function __clone() {}

    public function __wakeup(): never
    {
        throw new \LogicException('El Singleton Router no puede deserializarse.');
    }
}
