<?php
require_once __DIR__ . '/../config/database.php';

class ServiciosRepository
{
    public static function getAll(): array
    {
        $pdo = db();

        $sql = "SELECT id_servicio, nombre, descripcion, duracion, precio, activo
                FROM servicio
                ORDER BY nombre";

        $st = $pdo->prepare($sql);
        $st->execute();

        return $st->fetchAll();
    }

    public static function getActivos(): array
    {
        $pdo = db();

        $sql = "SELECT id_servicio, nombre, descripcion, duracion, precio, activo
                FROM servicio
                WHERE activo = 1
                ORDER BY nombre";

        $st = $pdo->prepare($sql);
        $st->execute();

        return $st->fetchAll();
    }

    public static function getById($id): array|false
    {
        $pdo = db();

        $sql = "SELECT id_servicio, nombre, descripcion, duracion, precio, activo
                FROM servicio
                WHERE id_servicio = :id";

        $st = $pdo->prepare($sql);
        $st->execute([
            ':id' => $id
        ]);

        return $st->fetch();
    }

    public static function create(string $nombre, ?string $descripcion, int $duracion, float $precio): bool
    {
        $pdo = db();

        $sql = "INSERT INTO servicio (nombre, descripcion, duracion, precio, activo)
                VALUES (:nombre, :descripcion, :duracion, :precio, 1)";

        $st = $pdo->prepare($sql);

        return $st->execute([
            ':nombre' => $nombre,
            ':descripcion' => $descripcion,
            ':duracion' => $duracion,
            ':precio' => $precio
        ]);
    }

    public static function update(int $id, string $nombre, ?string $descripcion, int $duracion, float $precio, int $activo): bool
    {
        $pdo = db();

        $sql = "UPDATE servicio
                SET nombre = :nombre,
                    descripcion = :descripcion,
                    duracion = :duracion,
                    precio = :precio,
                    activo = :activo
                WHERE id_servicio = :id";

        $st = $pdo->prepare($sql);
        $st->execute([
            ':nombre' => $nombre,
            ':descripcion' => $descripcion,
            ':duracion' => $duracion,
            ':precio' => $precio,
            ':activo' => $activo,
            ':id' => $id
        ]);

        return $st->rowCount() === 1;
    }

    public static function desactivar(int $id): bool
    {
        $pdo = db();

        $sql = "UPDATE servicio
                SET activo = 0
                WHERE id_servicio = :id
                  AND activo = 1";

        $st = $pdo->prepare($sql);
        $st->execute([
            ':id' => $id
        ]);

        return $st->rowCount() === 1;
    }

    public static function activar(int $id): bool
    {
        $pdo = db();

        $sql = "UPDATE servicio
                SET activo = 1
                WHERE id_servicio = :id
                  AND activo = 0";

        $st = $pdo->prepare($sql);
        $st->execute([
            ':id' => $id
        ]);

        return $st->rowCount() === 1;
    }

    public static function delete($id): bool
    {
        // Ya no se usa borrado físico. Se deja por compatibilidad interna.
        return self::desactivar((int) $id);
    }

    public static function tieneCitasFuturasReservadas(int $id_servicio): bool
    {
        $pdo = db();

        $sql = "SELECT COUNT(*) AS total
                FROM cita
                WHERE id_servicio = :id_servicio
                  AND estado = 'reservada'
                  AND (
                        fecha > CURDATE()
                        OR (fecha = CURDATE() AND hora_inicio > CURTIME())
                      )";

        $st = $pdo->prepare($sql);
        $st->execute([
            ':id_servicio' => $id_servicio
        ]);

        $fila = $st->fetch();

        return (int) ($fila['total'] ?? 0) > 0;
    }
}