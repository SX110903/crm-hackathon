<?php
declare(strict_types=1);

/**
 * UserModel — Gestiona los usuarios administradores del sistema.
 *
 * Responsabilidad única: acceso a datos de AdminUsers.
 * La contraseña NUNCA se almacena en texto plano; siempre bcrypt.
 */
class UserModel extends BaseModel
{
    protected string $table      = 'AdminUsers';
    protected string $primaryKey = 'UserID';

    // ─── Consultas de lectura ────────────────────────────────────────────────────

    /** Busca un usuario activo por nombre de usuario. */
    public function findByUsername(string $username): array|false
    {
        return $this->db->fetchOne(
            'SELECT * FROM AdminUsers WHERE Username = ? AND IsActive = 1',
            [$username]
        );
    }

    // ─── Verificación de credenciales ────────────────────────────────────────────

    /** Comprueba la contraseña usando verify de password_hash (bcrypt). */
    public function verifyPassword(string $plainPassword, string $storedHash): bool
    {
        return password_verify($plainPassword, $storedHash);
    }

    // ─── Escritura ───────────────────────────────────────────────────────────────

    /** Registra la fecha/hora del último login. */
    public function updateLastLogin(int $userId): void
    {
        $this->db->execute(
            'UPDATE AdminUsers SET LastLogin = NOW() WHERE UserID = ?',
            [$userId]
        );
    }

    /**
     * Crea un nuevo usuario administrador.
     * La contraseña se hashea con bcrypt (cost 12) antes de persistir.
     */
    public function create(array $cleanData): int
    {
        $this->db->execute(
            'INSERT INTO AdminUsers (Username, PasswordHash, Email, FullName, Role)
             VALUES (?, ?, ?, ?, ?)',
            [
                $cleanData['username'],
                password_hash($cleanData['password'], PASSWORD_BCRYPT, ['cost' => 12]),
                $cleanData['email'],
                $cleanData['fullName'],
                $cleanData['role'] ?? 'admin',
            ]
        );
        return $this->db->lastInsertId();
    }

    /** Actualiza nombre completo y email de un usuario. */
    public function update(int $id, array $cleanData): int
    {
        return $this->db->execute(
            'UPDATE AdminUsers SET FullName = ?, Email = ? WHERE UserID = ?',
            [$cleanData['fullName'], $cleanData['email'], $id]
        );
    }

    // ─── Validación ─────────────────────────────────────────────────────────────

    public function validate(array $rawData): array
    {
        return $this->mergeErrors(
            $this->requireFields([
                'username' => 'Nombre de usuario',
                'password' => 'Contraseña',
                'email'    => 'Email',
                'fullName' => 'Nombre completo',
            ], $rawData),
            $this->requireMaxLength('username', 'Nombre de usuario', 50, $rawData),
            $this->requireValidEmail('email', 'Email', $rawData)
        );
    }
}
