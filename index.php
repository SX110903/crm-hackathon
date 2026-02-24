<?php
declare(strict_types=1);

// ─── Punto de entrada único de la aplicación ────────────────────────────────
define('ROOT_PATH', __DIR__);

// ─── Sesión segura (antes de cualquier output) ──────────────────────────────
if (session_status() === PHP_SESSION_NONE) {
    session_set_cookie_params([
        'lifetime' => 0,          // Hasta que se cierre el navegador
        'path'     => '/',
        'secure'   => false,      // Cambiar a true en HTTPS (producción)
        'httponly' => true,       // No accesible desde JavaScript
        'samesite' => 'Lax',      // Protección CSRF básica en cookies
    ]);
    session_start();
}

// ─── 1. Cargador de variables de entorno (Singleton) ────────────────────────
require_once ROOT_PATH . '/core/EnvLoader.php';
EnvLoader::getInstance(ROOT_PATH . '/.env');

// ─── 2. Configuración (lee de EnvLoader) ────────────────────────────────────
require_once ROOT_PATH . '/config/config.php';

// ─── 3. Núcleo: Singleton de base de datos ───────────────────────────────────
require_once ROOT_PATH . '/core/Database.php';

// ─── 4. AuthGuard: Singleton de autenticación y CSRF ────────────────────────
require_once ROOT_PATH . '/core/AuthGuard.php';

// ─── 5. Router: Singleton despachador ───────────────────────────────────────
require_once ROOT_PATH . '/core/Router.php';

// ─── Manejo global de errores ────────────────────────────────────────────────
try {
    $router = Router::getInstance();
    $router->dispatch();
} catch (\Throwable $e) {
    http_response_code(503);
    $isPdo     = $e instanceof \PDOException;
    $errorType = $isPdo ? 'Error de conexión a la base de datos' : 'Error de la aplicación';
    $detail    = htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8');
    $file      = htmlspecialchars($e->getFile(), ENT_QUOTES, 'UTF-8');
    $line      = $e->getLine();

    // En producción nunca mostrar detalles del error
    $showDetails = (APP_ENV === 'development');

    echo '<!DOCTYPE html><html lang="es"><head><meta charset="UTF-8">'
        . '<title>' . $errorType . ' — ' . APP_NAME . '</title>'
        . '<style>'
        . 'body{font-family:sans-serif;background:#f1f5f9;display:flex;align-items:center;justify-content:center;min-height:100vh;margin:0}'
        . '.box{background:#fff;border-radius:8px;padding:2rem;max-width:600px;width:100%;box-shadow:0 4px 6px rgba(0,0,0,.07)}'
        . 'h1{color:#ef4444;margin:0 0 .75rem;font-size:1.3rem}'
        . 'p,li{color:#334155;font-size:.9rem;margin:.4rem 0}'
        . 'code{background:#f1f5f9;padding:.15rem .4rem;border-radius:4px;font-size:.8rem;font-family:monospace}'
        . 'a{color:#2563eb}'
        . '</style></head><body><div class="box">'
        . '<h1>' . $errorType . '</h1>';

    if ($showDetails) {
        echo '<p><strong>Mensaje:</strong> <code>' . $detail . '</code></p>'
            . '<p><strong>Archivo:</strong> <code>' . $file . ':' . $line . '</code></p>';

        if ($isPdo) {
            echo '<hr style="border:none;border-top:1px solid #e2e8f0;margin:1rem 0">'
                . '<p><strong>Configuración DB:</strong></p><ul>'
                . '<li>Host: <code>' . DB_HOST . '</code></li>'
                . '<li>Puerto: <code>' . DB_PORT . '</code></li>'
                . '<li>Base de datos: <code>' . DB_NAME . '</code></li>'
                . '<li>Usuario: <code>' . DB_USER . '</code></li>'
                . '</ul><p>Comprueba que MySQL esté activo y que la BD <code>' . DB_NAME . '</code> exista.</p>';
        }
    } else {
        echo '<p>Se ha producido un error interno. Por favor, contacta con el administrador.</p>';
    }

    echo '<p style="margin-top:1rem"><a href="' . BASE_URL . '">← Volver al inicio</a></p>'
        . '</div></body></html>';
}
