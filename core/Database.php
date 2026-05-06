<?php
declare(strict_types=1);

/**
 * Database — Singleton que gestiona la única conexión PDO de la aplicación.
 * Toda interacción con la base de datos pasa por esta clase.
 */
final class Database
{
    private static ?self $instance = null;
    private PDO $pdo;

    // ─── Constructor privado: impide instanciación externa ──────────────────────
    private function __construct()
    {
        $dsn = sprintf(
            'mysql:host=%s;port=%d;dbname=%s;charset=%s',
            DB_HOST, DB_PORT, DB_NAME, DB_CHARSET
        );

        $this->pdo = new PDO($dsn, DB_USER, DB_PASS, [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ]);
    }

    // ─── Punto de acceso global ─────────────────────────────────────────────────
    public static function getInstance(): self
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    // ─── API de consultas ───────────────────────────────────────────────────────

    /** Retorna todos los registros de una consulta preparada. */
    public function fetchAll(string $sql, array $params = []): array
    {
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    /** Retorna un único registro o false si no existe. */
    public function fetchOne(string $sql, array $params = []): array|false
    {
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetch();
    }

    /** Retorna un valor escalar (COUNT, SUM…) o null. */
    public function fetchScalar(string $sql, array $params = []): mixed
    {
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchColumn();
    }

    /** Ejecuta INSERT / UPDATE / DELETE y retorna el número de filas afectadas. */
    public function execute(string $sql, array $params = []): int
    {
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->rowCount();
    }

    /** ID del último INSERT. */
    public function lastInsertId(): int
    {
        return (int) $this->pdo->lastInsertId();
    }

    // ─── Control de transacciones ───────────────────────────────────────────────
    public function beginTransaction(): void { $this->pdo->beginTransaction(); }
    public function commit(): void           { $this->pdo->commit(); }
    public function rollback(): void         { $this->pdo->rollBack(); }

    // ─── Singleton: impide clonar y deserializar ────────────────────────────────
    private function __clone() {}

    public function __wakeup(): never
    {
        throw new \LogicException('El Singleton Database no puede deserializarse.');
    }
}
