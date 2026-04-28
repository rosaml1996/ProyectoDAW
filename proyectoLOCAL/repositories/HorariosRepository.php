<?php
require_once __DIR__ . '/../config/database.php';

class HorariosRepository
{
    public static function getAll(): array
    {
        $pdo = db();

        $sql = "SELECT id_horario, dia_semana, hora_inicio, hora_fin, activo
                FROM horario_laboral
                ORDER BY dia_semana, hora_inicio";

        $st = $pdo->prepare($sql);
        $st->execute();

        return $st->fetchAll();
    }

    public static function getById(int $id_horario): array|false
    {
        $pdo = db();

        $sql = "SELECT id_horario, dia_semana, hora_inicio, hora_fin, activo
                FROM horario_laboral
                WHERE id_horario = :id_horario";

        $st = $pdo->prepare($sql);
        $st->execute([
            ':id_horario' => $id_horario
        ]);

        return $st->fetch();
    }

    public static function create(int $dia_semana, string $hora_inicio, string $hora_fin, int $activo = 1): int|false
    {
        $pdo = db();

        $sql = "INSERT INTO horario_laboral (dia_semana, hora_inicio, hora_fin, activo)
                VALUES (:dia_semana, :hora_inicio, :hora_fin, :activo)";

        $st = $pdo->prepare($sql);

        $ok = $st->execute([
            ':dia_semana' => $dia_semana,
            ':hora_inicio' => $hora_inicio,
            ':hora_fin' => $hora_fin,
            ':activo' => $activo
        ]);

        if (!$ok) {
            return false;
        }

        return (int) $pdo->lastInsertId();
    }

    public static function update(int $id_horario, int $dia_semana, string $hora_inicio, string $hora_fin, int $activo): bool
    {
        $pdo = db();

        $sql = "UPDATE horario_laboral
                SET dia_semana = :dia_semana,
                    hora_inicio = :hora_inicio,
                    hora_fin = :hora_fin,
                    activo = :activo
                WHERE id_horario = :id_horario";

        $st = $pdo->prepare($sql);
        $st->execute([
            ':dia_semana' => $dia_semana,
            ':hora_inicio' => $hora_inicio,
            ':hora_fin' => $hora_fin,
            ':activo' => $activo,
            ':id_horario' => $id_horario
        ]);

        return $st->rowCount() === 1;
    }

    public static function delete(int $id_horario): bool
    {
        $pdo = db();

        $sql = "DELETE FROM horario_laboral
                WHERE id_horario = :id_horario";

        $st = $pdo->prepare($sql);
        $st->execute([
            ':id_horario' => $id_horario
        ]);

        return $st->rowCount() === 1;
    }

    public static function existeSolape(int $dia_semana, string $hora_inicio, string $hora_fin, ?int $ignorarId = null): bool
    {
        $pdo = db();

        $sql = "SELECT COUNT(*) AS total
                FROM horario_laboral
                WHERE dia_semana = :dia_semana
                  AND (
                        :hora_inicio < hora_fin
                        AND :hora_fin > hora_inicio
                      )";

        $params = [
            ':dia_semana' => $dia_semana,
            ':hora_inicio' => $hora_inicio,
            ':hora_fin' => $hora_fin
        ];

        if ($ignorarId !== null) {
            $sql .= " AND id_horario <> :ignorar_id";
            $params[':ignorar_id'] = $ignorarId;
        }

        $st = $pdo->prepare($sql);
        $st->execute($params);

        $fila = $st->fetch();

        return (int) ($fila['total'] ?? 0) > 0;
    }
}