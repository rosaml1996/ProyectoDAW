<?php
require_once __DIR__ . '/../config/database.php';

class CitasRepository
{
    public static function getDisponibles(string $fecha, int $id_servicio): array
    {
        $pdo = db();

        $servicio = self::getServicioActivo($id_servicio);
        if (!$servicio) {
            return [];
        }

        // No permitir fechas pasadas
        $hoy = date('Y-m-d');
        if ($fecha < $hoy) {
            return [];
        }

        $duracion = (int) $servicio['duracion'];
        $diaSemana = (int) date('N', strtotime($fecha));

        // 1. Horario laboral del día
        $sqlHorarios = "SELECT hora_inicio, hora_fin
                        FROM horario_laboral
                        WHERE dia_semana = :dia_semana
                          AND activo = 1
                        ORDER BY hora_inicio";
        $stHorarios = $pdo->prepare($sqlHorarios);
        $stHorarios->execute([':dia_semana' => $diaSemana]);
        $horarios = $stHorarios->fetchAll();

        if (!$horarios) {
            return [];
        }

        // 2. Bloqueo de día completo
        $sqlBloqueoCompleto = "SELECT COUNT(*) AS total
                               FROM bloqueo_agenda
                               WHERE fecha = :fecha
                                 AND hora_inicio IS NULL
                                 AND hora_fin IS NULL";
        $stBloqueoCompleto = $pdo->prepare($sqlBloqueoCompleto);
        $stBloqueoCompleto->execute([':fecha' => $fecha]);
        $filaBloqueoCompleto = $stBloqueoCompleto->fetch();
        $bloqueoCompleto = (int) ($filaBloqueoCompleto['total'] ?? 0) > 0;

        if ($bloqueoCompleto) {
            return [];
        }

        // 3. Bloqueos parciales
        $sqlBloqueos = "SELECT hora_inicio, hora_fin
                        FROM bloqueo_agenda
                        WHERE fecha = :fecha
                          AND hora_inicio IS NOT NULL
                          AND hora_fin IS NOT NULL";
        $stBloqueos = $pdo->prepare($sqlBloqueos);
        $stBloqueos->execute([':fecha' => $fecha]);
        $bloqueos = $stBloqueos->fetchAll();

        // 4. Citas ocupadas de ese día
        $sqlCitas = "SELECT hora_inicio, hora_fin
                     FROM cita
                     WHERE fecha = :fecha
                       AND estado = 'reservada'";
        $stCitas = $pdo->prepare($sqlCitas);
        $stCitas->execute([':fecha' => $fecha]);
        $citasOcupadas = $stCitas->fetchAll();

        $intervalo = 30; // minutos
        $resultado = [];

        foreach ($horarios as $tramo) {
            $inicioTramo = new DateTime($fecha . ' ' . $tramo['hora_inicio']);
            $finTramo = new DateTime($fecha . ' ' . $tramo['hora_fin']);

            $actual = clone $inicioTramo;

            while (true) {
                $inicioSlot = clone $actual;
                $finSlot = clone $actual;
                $finSlot->modify("+{$duracion} minutes");

                if ($finSlot > $finTramo) {
                    break;
                }

                // No ofrecer horas pasadas si la fecha es hoy
                $ahora = new DateTime();
                if ($inicioSlot->format('Y-m-d') === $ahora->format('Y-m-d') && $inicioSlot < $ahora) {
                    $actual->modify("+{$intervalo} minutes");
                    continue;
                }

                $ocupado = false;

                foreach ($bloqueos as $bloqueo) {
                    $bInicio = new DateTime($fecha . ' ' . $bloqueo['hora_inicio']);
                    $bFin = new DateTime($fecha . ' ' . $bloqueo['hora_fin']);

                    if (self::solapa($inicioSlot, $finSlot, $bInicio, $bFin)) {
                        $ocupado = true;
                        break;
                    }
                }

                if (!$ocupado) {
                    foreach ($citasOcupadas as $cita) {
                        $cInicio = new DateTime($fecha . ' ' . $cita['hora_inicio']);
                        $cFin = new DateTime($fecha . ' ' . $cita['hora_fin']);

                        if (self::solapa($inicioSlot, $finSlot, $cInicio, $cFin)) {
                            $ocupado = true;
                            break;
                        }
                    }
                }

                if (!$ocupado) {
                    $resultado[] = [
                        'fecha' => $fecha,
                        'hora' => $inicioSlot->format('H:i:s'),
                        'hora_inicio' => $inicioSlot->format('H:i:s'),
                        'hora_fin' => $finSlot->format('H:i:s'),
                        'id_servicio' => $servicio['id_servicio'],
                        'servicio' => $servicio['nombre'],
                        'duracion' => $servicio['duracion'],
                        'precio' => $servicio['precio']
                    ];
                }

                $actual->modify("+{$intervalo} minutes");
            }
        }

        return $resultado;
    }

    public static function getByPaciente(int $id_paciente): array
    {
        $pdo = db();

        $sql = "SELECT c.id_cita,
                       c.fecha,
                       c.hora_inicio AS hora,
                       c.hora_inicio,
                       c.hora_fin,
                       c.estado,
                       c.id_servicio,
                       s.nombre AS servicio,
                       s.duracion,
                       s.precio
                FROM cita c
                INNER JOIN servicio s ON s.id_servicio = c.id_servicio
                WHERE c.id_paciente = :id_paciente
                ORDER BY c.fecha DESC, c.hora_inicio DESC";

        $st = $pdo->prepare($sql);
        $st->execute([
            ':id_paciente' => $id_paciente
        ]);

        return $st->fetchAll();
    }

    public static function getById(int $id_cita): array|false
    {
        $pdo = db();

        $sql = "SELECT c.id_cita,
                       c.fecha,
                       c.hora_inicio AS hora,
                       c.hora_inicio,
                       c.hora_fin,
                       c.estado,
                       c.id_paciente,
                       c.id_servicio,
                       s.nombre AS servicio,
                       s.duracion,
                       s.precio
                FROM cita c
                INNER JOIN servicio s ON s.id_servicio = c.id_servicio
                WHERE c.id_cita = :id_cita";

        $st = $pdo->prepare($sql);
        $st->execute([
            ':id_cita' => $id_cita
        ]);

        return $st->fetch();
    }

    public static function reservar(string $fecha, string $hora_inicio, int $id_servicio, int $id_paciente): int|false
    {
        $pdo = db();

        $servicio = self::getServicioActivo($id_servicio);
        if (!$servicio) {
            return false;
        }

        $duracion = (int) $servicio['duracion'];
        $hora_fin = date('H:i:s', strtotime($hora_inicio . " +{$duracion} minutes"));

        if (!self::esHorarioDisponible($fecha, $hora_inicio, $hora_fin, $id_servicio)) {
            return false;
        }

        $sql = "INSERT INTO cita (fecha, hora_inicio, hora_fin, estado, id_paciente, id_servicio)
                VALUES (:fecha, :hora_inicio, :hora_fin, 'reservada', :id_paciente, :id_servicio)";

        $st = $pdo->prepare($sql);
        $ok = $st->execute([
            ':fecha' => $fecha,
            ':hora_inicio' => $hora_inicio,
            ':hora_fin' => $hora_fin,
            ':id_paciente' => $id_paciente,
            ':id_servicio' => $id_servicio
        ]);

        if (!$ok) {
            return false;
        }

        return (int) $pdo->lastInsertId();
    }

    public static function anular(int $id_cita, int $id_paciente): bool
    {
        $pdo = db();

        $sql = "UPDATE cita
                SET estado = 'cancelada'
                WHERE id_cita = :id_cita
                  AND id_paciente = :id_paciente
                  AND estado = 'reservada'";

        $st = $pdo->prepare($sql);
        $st->execute([
            ':id_cita' => $id_cita,
            ':id_paciente' => $id_paciente
        ]);

        return $st->rowCount() === 1;
    }

    public static function cancelarAdmin(int $id_cita): bool
    {
        $pdo = db();

        $sql = "UPDATE cita
                SET estado = 'cancelada'
                WHERE id_cita = :id_cita
                  AND estado = 'reservada'";

        $st = $pdo->prepare($sql);
        $st->execute([
            ':id_cita' => $id_cita
        ]);

        return $st->rowCount() === 1;
    }

    public static function eliminar(int $id_cita): bool
    {
        $pdo = db();

        $sql = "DELETE FROM cita
                WHERE id_cita = :id_cita";

        $st = $pdo->prepare($sql);
        $st->execute([
            ':id_cita' => $id_cita
        ]);

        return $st->rowCount() === 1;
    }

    public static function getAll(): array
    {
        $pdo = db();

        $sql = "SELECT c.id_cita,
                       c.fecha,
                       c.hora_inicio AS hora,
                       c.hora_inicio,
                       c.hora_fin,
                       c.estado,
                       c.id_paciente,
                       c.id_servicio,
                       s.nombre AS servicio,
                       s.duracion,
                       s.precio,
                       p.nombre AS paciente
                FROM cita c
                INNER JOIN servicio s ON s.id_servicio = c.id_servicio
                LEFT JOIN paciente p ON p.id_paciente = c.id_paciente
                ORDER BY c.fecha DESC, c.hora_inicio DESC";

        $st = $pdo->prepare($sql);
        $st->execute();

        return $st->fetchAll();
    }

    private static function getServicioActivo(int $id_servicio): array|false
    {
        $pdo = db();

        $sql = "SELECT id_servicio, nombre, duracion, precio, activo
                FROM servicio
                WHERE id_servicio = :id_servicio
                  AND activo = 1";

        $st = $pdo->prepare($sql);
        $st->execute([':id_servicio' => $id_servicio]);

        return $st->fetch();
    }

    private static function esHorarioDisponible(string $fecha, string $hora_inicio, string $hora_fin, int $id_servicio): bool
    {
        $disponibles = self::getDisponibles($fecha, $id_servicio);

        foreach ($disponibles as $slot) {
            if ($slot['hora_inicio'] === $hora_inicio && $slot['hora_fin'] === $hora_fin) {
                return true;
            }
        }

        return false;
    }

    private static function solapa(DateTime $inicioA, DateTime $finA, DateTime $inicioB, DateTime $finB): bool
    {
        return $inicioA < $finB && $finA > $inicioB;
    }
}