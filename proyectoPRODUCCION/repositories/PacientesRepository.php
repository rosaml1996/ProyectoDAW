<?php
require_once __DIR__ . '/../config/database.php';

class PacientesRepository
{
    public static function findByEmail(string $email): array|false
    {
        $pdo = db();

        $sql = "SELECT id_paciente, nombre, fecha_nacimiento, telefono, email, contraseña
                FROM paciente
                WHERE email = :email";

        $st = $pdo->prepare($sql);
        $st->execute([
            ':email' => $email
        ]);

        return $st->fetch();
    }

    public static function findById(int $id): array|false
    {
        $pdo = db();

        $sql = "SELECT id_paciente, nombre, fecha_nacimiento, telefono, email
                FROM paciente
                WHERE id_paciente = :id_paciente";

        $st = $pdo->prepare($sql);
        $st->execute([
            ':id_paciente' => $id
        ]);

        return $st->fetch();
    }

    static function crear($nombre, $fechaNacimiento, $telefono, $email, $contrasena): int|false
    {
        $pdo = db();

        $sql = "INSERT INTO paciente (nombre, fecha_nacimiento, telefono, email, contraseña)
            VALUES (:nombre, :fecha_nacimiento, :telefono, :email, :contrasena)";

        $st = $pdo->prepare($sql);

        $ok = $st->execute([
            ':nombre' => $nombre,
            ':fecha_nacimiento' => $fechaNacimiento,
            ':telefono' => $telefono,
            ':email' => $email,
            ':contrasena' => $contrasena
        ]);

        if (!$ok) {
            return false;
        }

        return (int) $pdo->lastInsertId();
    }

    public static function actualizar($id, $nombre, $fechaNacimiento, $telefono, $email): bool
    {
        $pdo = db();

        $sql = "UPDATE paciente
            SET nombre = :nombre,
                fecha_nacimiento = :fecha_nacimiento,
                telefono = :telefono,
                email = :email
            WHERE id_paciente = :id_paciente";

        $st = $pdo->prepare($sql);

        $st->execute([
            ':nombre' => $nombre,
            ':fecha_nacimiento' => $fechaNacimiento,
            ':telefono' => $telefono,
            ':email' => $email,
            ':id_paciente' => $id
        ]);

        return $st->rowCount() >= 0;
    }

    public static function actualizarClave($id, $hash): bool
    {
        $pdo = db();

        $sql = "UPDATE paciente
            SET contraseña = :contrasena
            WHERE id_paciente = :id_paciente";

        $st = $pdo->prepare($sql);

        $st->execute([
            ':contrasena' => $hash,
            ':id_paciente' => $id
        ]);

        return $st->rowCount() >= 0;
    }

    public static function getPerfilCompleto(int $id): array|false
    {
        $pdo = db();

        $sql = "SELECT id_paciente, nombre, fecha_nacimiento, telefono, email
            FROM paciente
            WHERE id_paciente = :id_paciente";

        $st = $pdo->prepare($sql);
        $st->execute([
            ':id_paciente' => $id
        ]);

        return $st->fetch();
    }
}