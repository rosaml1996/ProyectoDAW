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

    public static function update(int $id_bloqueo, string $fecha, ?string $hora_inicio, ?string $hora_fin, ?string $motivo): bool
    {
        $pdo = db();

        $sql = "UPDATE bloqueo_agenda
                SET fecha = :fecha,
                    hora_inicio = :hora_inicio,
                    hora_fin = :hora_fin,
                    motivo = :motivo
                WHERE id_bloqueo = :id_bloqueo";

        $st = $pdo->prepare($sql);
        $st->execute([
            ':fecha' => $fecha,
            ':hora_inicio' => $hora_inicio,
            ':hora_fin' => $hora_fin,
            ':motivo' => $motivo,
            ':id_bloqueo' => $id_bloqueo
        ]);

        return $st->rowCount() === 1;
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

    public static function existeSolape(string $fecha, ?string $hora_inicio, ?string $hora_fin, ?int $ignorarId = null): bool
    {
        $pdo = db();

        if ($hora_inicio === null && $hora_fin === null) {
            $sql = "SELECT COUNT(*) AS total
                    FROM bloqueo_agenda
                    WHERE fecha = :fecha";

            $params = [
                ':fecha' => $fecha
            ];

            if ($ignorarId !== null) {
                $sql .= " AND id_bloqueo <> :ignorar_id";
                $params[':ignorar_id'] = $ignorarId;
            }

            $st = $pdo->prepare($sql);
            $st->execute($params);
            $fila = $st->fetch();

            return (int) ($fila['total'] ?? 0) > 0;
        }

        $sql = "SELECT COUNT(*) AS total
                FROM bloqueo_agenda
                WHERE fecha = :fecha
                  AND (
                        (hora_inicio IS NULL AND hora_fin IS NULL)
                        OR (
                            hora_inicio IS NOT NULL
                            AND hora_fin IS NOT NULL
                            AND :hora_inicio < hora_fin
                            AND :hora_fin > hora_inicio
                        )
                      )";

        $params = [
            ':fecha' => $fecha,
            ':hora_inicio' => $hora_inicio,
            ':hora_fin' => $hora_fin
        ];

        if ($ignorarId !== null) {
            $sql .= " AND id_bloqueo <> :ignorar_id";
            $params[':ignorar_id'] = $ignorarId;
        }

        $st = $pdo->prepare($sql);
        $st->execute($params);
        $fila = $st->fetch();

        return (int) ($fila['total'] ?? 0) > 0;
    }
}