<?php
require_once __DIR__ . '/../config/database.php';

class BloqueosRepository
{
    public static function getAll(): array
    {
        $pdo = db();

        $sql = "SELECT id_bloqueo, fecha, hora_inicio, hora_fin, motivo
                FROM bloqueo_agenda
                ORDER BY fecha DESC, hora_inicio ASC";

        $st = $pdo->prepare($sql);
        $st->execute();

        return $st->fetchAll();
    }

    public static function getById(int $id_bloqueo): array|false
    {
        $pdo = db();

        $sql = "SELECT id_bloqueo, fecha, hora_inicio, hora_fin, motivo
                FROM bloqueo_agenda
                WHERE id_bloqueo = :id_bloqueo";

        $st = $pdo->prepare($sql);
        $st->execute([
            ':id_bloqueo' => $id_bloqueo
        ]);

        return $st->fetch();
    }

    public static function create(string $fecha, ?string $hora_inicio, ?string $hora_fin, ?string $motivo): int|false
    {
        $pdo = db();

        $sql = "INSERT INTO bloqueo_agenda (fecha, hora_inicio, hora_fin, motivo)
                VALUES (:fecha, :hora_inicio, :hora_fin, :motivo)";

        $st = $pdo->prepare($sql);

        $ok = $st->execute([
            ':fecha' => $fecha,
            ':hora_inicio' => $hora_inicio,
            ':hora_fin' => $hora_fin,
            ':motivo' => $motivo
        ]);

        if (!$ok) {
            return false;
        }

        return (int) $pdo->lastInsertId();
    }

    public static function delete(int $id_bloqueo): bool
    {
        $pdo = db();

        $sql = "DELETE FROM bloqueo_agenda
                WHERE id_bloqueo = :id_bloqueo";

        $st = $pdo->prepare($sql);
        $st->execute([
            ':id_bloqueo' => $id_bloqueo
        ]);

        return $st->rowCount() === 1;
    }
}