<?php
declare(strict_types=1);

/**
 * BaseModel — Clase abstracta base para todos los modelos.
 *
 * Centraliza:
 *  - Acceso al Singleton Database
 *  - CRUD genérico (findAll, findById, count, delete)
 *  - Helpers de validación reutilizables por herencia
 *
 * Subclases deben definir: $table, $primaryKey
 * Subclases deben implementar: create(), update(), validate()
 */
abstract class BaseModel
{
    protected Database $db;

    /** Nombre de la tabla en la BD (definido por cada subclase). */
    protected string $table;

    /** Clave primaria de la tabla (definido por cada subclase). */
    protected string $primaryKey;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    // ═══════════════════════════════════════════════════════════════════════════
    // CRUD GENÉRICO — Reutilizable por herencia, sin repetir código
    // ═══════════════════════════════════════════════════════════════════════════

    /** Devuelve una página de registros ordenados por PK descendente. */
    public function findAll(int $page = 1): array
    {
        $offset = ($page - 1) * RECORDS_PER_PAGE;
        return $this->db->fetchAll(
            "SELECT * FROM `{$this->table}`
             ORDER BY `{$this->primaryKey}` DESC
             LIMIT ? OFFSET ?",
            [RECORDS_PER_PAGE, $offset]
        );
    }

    /** Devuelve un registro por PK o false si no existe. */
    public function findById(int $id): array|false
    {
        return $this->db->fetchOne(
            "SELECT * FROM `{$this->table}` WHERE `{$this->primaryKey}` = ?",
            [$id]
        );
    }

    /** Total de registros en la tabla (para paginación). */
    public function count(): int
    {
        return (int) $this->db->fetchScalar(
            "SELECT COUNT(*) FROM `{$this->table}`"
        );
    }

    /** Elimina un registro por PK. Devuelve filas afectadas. */
    public function delete(int $id): int
    {
        return $this->db->execute(
            "DELETE FROM `{$this->table}` WHERE `{$this->primaryKey}` = ?",
            [$id]
        );
    }

    // ═══════════════════════════════════════════════════════════════════════════
    // CONTRATO — Cada modelo implementa su propia lógica de negocio
    // ═══════════════════════════════════════════════════════════════════════════

    /** Crea un registro. Devuelve el ID insertado. */
    abstract public function create(array $cleanData): int;

    /** Actualiza un registro. Devuelve filas afectadas. */
    abstract public function update(int $id, array $cleanData): int;

    /**
     * Valida datos de entrada.
     * @return string[] Lista de mensajes de error; vacío = válido.
     */
    abstract public function validate(array $rawData): array;

    // ═══════════════════════════════════════════════════════════════════════════
    // HELPERS DE VALIDACIÓN — Reutilizables desde subclases
    // ═══════════════════════════════════════════════════════════════════════════

    /**
     * Verifica que los campos indicados no estén vacíos.
     * @param array<string,string> $rules  [campo => etiqueta]
     */
    protected function requireFields(array $rules, array $data): array
    {
        $errors = [];
        foreach ($rules as $field => $label) {
            if (!isset($data[$field]) || trim((string) $data[$field]) === '') {
                $errors[] = "El campo «{$label}» es obligatorio.";
            }
        }
        return $errors;
    }

    /** Verifica que un campo numérico esté dentro de un rango. */
    protected function requireNumericRange(
        string $field,
        string $label,
        float  $min,
        float  $max,
        array  $data
    ): array {
        if (!isset($data[$field]) || $data[$field] === '') {
            return [];
        }
        $value = (float) $data[$field];
        if ($value < $min || $value > $max) {
            return ["«{$label}» debe estar entre {$min} y {$max}."];
        }
        return [];
    }

    /** Verifica que el valor esté en una lista permitida. */
    protected function requireInList(
        string $field,
        string $label,
        array  $allowed,
        array  $data
    ): array {
        if (!isset($data[$field]) || $data[$field] === '') {
            return [];
        }
        if (!in_array($data[$field], $allowed, true)) {
            return ["«{$label}» contiene un valor no permitido."];
        }
        return [];
    }

    /** Verifica que un campo sea un email válido. */
    protected function requireValidEmail(string $field, string $label, array $data): array
    {
        if (!isset($data[$field]) || $data[$field] === '') {
            return [];
        }
        if (!filter_var($data[$field], FILTER_VALIDATE_EMAIL)) {
            return ["«{$label}» no es un email válido."];
        }
        return [];
    }

    /** Verifica longitud máxima de un string. */
    protected function requireMaxLength(string $field, string $label, int $max, array $data): array
    {
        if (!isset($data[$field])) {
            return [];
        }
        if (mb_strlen((string) $data[$field]) > $max) {
            return ["«{$label}» no puede superar {$max} caracteres."];
        }
        return [];
    }

    /** Merge de múltiples arrays de errores en uno. */
    protected function mergeErrors(array ...$errorGroups): array
    {
        return array_merge(...$errorGroups);
    }

    // ═══════════════════════════════════════════════════════════════════════════
    // PAGINACIÓN — Cálculo reutilizable
    // ═══════════════════════════════════════════════════════════════════════════

    public function buildPagination(int $totalRecords, int $currentPage): array
    {
        $totalPages = max(1, (int) ceil($totalRecords / RECORDS_PER_PAGE));
        return [
            'currentPage'  => max(1, min($currentPage, $totalPages)),
            'totalPages'   => $totalPages,
            'totalRecords' => $totalRecords,
            'perPage'      => RECORDS_PER_PAGE,
        ];
    }
}
