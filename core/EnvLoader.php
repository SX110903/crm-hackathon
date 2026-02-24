<?php
declare(strict_types=1);

/**
 * EnvLoader — Singleton que carga y expone las variables del fichero .env.
 *
 * Patrón: Singleton.
 * Responsabilidad única: parsear .env y proveer un acceso tipado a sus valores.
 * Las variables también se inyectan en $_ENV y putenv() para máxima compatibilidad.
 */
final class EnvLoader
{
    private static ?self $instance = null;

    /** @var array<string,string> */
    private array $vars = [];

    // ─── Constructor privado: carga el fichero en construcción ──────────────────
    private function __construct(string $filePath)
    {
        if (file_exists($filePath)) {
            $this->parse($filePath);
        }
    }

    // ─── Punto de acceso global ──────────────────────────────────────────────────
    public static function getInstance(string $filePath = ''): self
    {
        if (self::$instance === null) {
            self::$instance = new self($filePath);
        }
        return self::$instance;
    }

    // ─── API pública ─────────────────────────────────────────────────────────────

    public function get(string $key, string $default = ''): string
    {
        return $this->vars[$key] ?? $default;
    }

    public function getInt(string $key, int $default = 0): int
    {
        return isset($this->vars[$key]) ? (int) $this->vars[$key] : $default;
    }

    public function getBool(string $key, bool $default = false): bool
    {
        if (!isset($this->vars[$key])) {
            return $default;
        }
        return in_array(strtolower($this->vars[$key]), ['true', '1', 'yes', 'on'], true);
    }

    // ─── Parser interno ──────────────────────────────────────────────────────────

    private function parse(string $filePath): void
    {
        $lines = file($filePath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        if ($lines === false) {
            return;
        }

        foreach ($lines as $line) {
            $line = trim($line);

            // Ignorar comentarios y líneas sin '='
            if ($line === '' || str_starts_with($line, '#') || !str_contains($line, '=')) {
                continue;
            }

            [$name, $value] = explode('=', $line, 2);
            $name  = trim($name);
            $value = trim($value);

            // Eliminar comillas envolventes (simples o dobles)
            if (strlen($value) >= 2) {
                $first = $value[0];
                $last  = $value[-1];
                if (($first === '"' && $last === '"') || ($first === "'" && $last === "'")) {
                    $value = substr($value, 1, -1);
                }
            }

            // Eliminar comentarios inline: VAR=valor # comentario
            if (str_contains($value, ' #')) {
                $value = trim(explode(' #', $value, 2)[0]);
            }

            $this->vars[$name] = $value;
            $_ENV[$name]       = $value;
            putenv("{$name}={$value}");
        }
    }

    // ─── Singleton guards ────────────────────────────────────────────────────────
    private function __clone() {}

    public function __wakeup(): never
    {
        throw new \LogicException('EnvLoader Singleton no puede deserializarse.');
    }
}

// ─── Funciones helper globales (acceso rápido en config.php) ────────────────

function env(string $key, string $default = ''): string
{
    return EnvLoader::getInstance()->get($key, $default);
}

function envInt(string $key, int $default = 0): int
{
    return EnvLoader::getInstance()->getInt($key, $default);
}
