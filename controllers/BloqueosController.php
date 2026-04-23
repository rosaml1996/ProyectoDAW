<?php
require_once __DIR__ . '/../repositories/BloqueosRepository.php';
require_once __DIR__ . '/../helpers/Response.php';
require_once __DIR__ . '/../helpers/Request.php';
require_once __DIR__ . '/../security/AdminAuth.php';

class BloqueosController
{
    public static function getAll(): void
    {
        try {
            AdminAuth::user();
            $bloqueos = BloqueosRepository::getAll();
            Response::json($bloqueos);
        } catch (Throwable $e) {
            Response::json(['error' => 'No se pudieron cargar los bloqueos.'], 500);
        }
    }

    public static function create(): void
    {
        try {
            AdminAuth::user();

            $data = Request::json();

            $fecha = trim((string) ($data['fecha'] ?? ''));
            $hora_inicio = trim((string) ($data['hora_inicio'] ?? ''));
            $hora_fin = trim((string) ($data['hora_fin'] ?? ''));
            $motivo = trim((string) ($data['motivo'] ?? ''));

            if ($fecha === '') {
                Response::json(['error' => 'Debes indicar una fecha.'], 400);
            }

            if (!self::validarFecha($fecha)) {
                Response::json(['error' => 'La fecha debe tener formato YYYY-MM-DD.'], 400);
            }

            if ($fecha < date('Y-m-d')) {
                Response::json(['error' => 'No se puede crear un bloqueo en una fecha pasada.'], 400);
            }

            if ($motivo === '') {
                Response::json(['error' => 'Debes indicar un motivo para el bloqueo.'], 400);
            }

            $hora_inicio = $hora_inicio === '' ? null : self::normalizarHora($hora_inicio);
            $hora_fin = $hora_fin === '' ? null : self::normalizarHora($hora_fin);

            $esDiaCompleto = ($hora_inicio === null && $hora_fin === null);
            $esParcialValido = ($hora_inicio !== null && $hora_fin !== null);

            if (!$esDiaCompleto && !$esParcialValido) {
                Response::json(['error' => 'Debes indicar ambas horas o dejar ambas vacías para bloquear el día completo.'], 400);
            }

            if ($esParcialValido) {
                if (!self::validarHora($hora_inicio) || !self::validarHora($hora_fin)) {
                    Response::json(['error' => 'Las horas deben tener formato HH:MM:SS.'], 400);
                }

                if ($hora_inicio >= $hora_fin) {
                    Response::json(['error' => 'La hora de inicio debe ser menor que la hora de fin.'], 400);
                }
            }

            if (BloqueosRepository::existeSolape($fecha, $hora_inicio, $hora_fin)) {
                Response::json(['error' => 'Ya existe un bloqueo igual o solapado para esa fecha.'], 409);
            }

            $id = BloqueosRepository::create($fecha, $hora_inicio, $hora_fin, $motivo);

            if (!$id) {
                Response::json(['error' => 'No se pudo guardar el bloqueo.'], 500);
            }

            Response::json([
                'message' => 'Bloqueo guardado correctamente.',
                'id_bloqueo' => $id
            ], 201);
        } catch (Throwable $e) {
            Response::json(['error' => 'No se pudo guardar el bloqueo.'], 500);
        }
    }

    public static function update(int $id): void
    {
        try {
            AdminAuth::user();

            $bloqueo = BloqueosRepository::getById($id);

            if (!$bloqueo) {
                Response::json(['error' => 'El bloqueo no existe.'], 404);
            }

            $data = Request::json();

            $fecha = trim((string) ($data['fecha'] ?? ''));
            $hora_inicio = trim((string) ($data['hora_inicio'] ?? ''));
            $hora_fin = trim((string) ($data['hora_fin'] ?? ''));
            $motivo = trim((string) ($data['motivo'] ?? ''));

            if ($fecha === '') {
                Response::json(['error' => 'Debes indicar una fecha.'], 400);
            }

            if (!self::validarFecha($fecha)) {
                Response::json(['error' => 'La fecha debe tener formato YYYY-MM-DD.'], 400);
            }

            if ($fecha < date('Y-m-d')) {
                Response::json(['error' => 'No se puede modificar un bloqueo a una fecha pasada.'], 400);
            }

            if ($motivo === '') {
                Response::json(['error' => 'Debes indicar un motivo para el bloqueo.'], 400);
            }

            $hora_inicio = $hora_inicio === '' ? null : self::normalizarHora($hora_inicio);
            $hora_fin = $hora_fin === '' ? null : self::normalizarHora($hora_fin);

            $esDiaCompleto = ($hora_inicio === null && $hora_fin === null);
            $esParcialValido = ($hora_inicio !== null && $hora_fin !== null);

            if (!$esDiaCompleto && !$esParcialValido) {
                Response::json(['error' => 'Debes indicar ambas horas o dejar ambas vacías para bloquear el día completo.'], 400);
            }

            if ($esParcialValido) {
                if (!self::validarHora($hora_inicio) || !self::validarHora($hora_fin)) {
                    Response::json(['error' => 'Las horas deben tener formato HH:MM:SS.'], 400);
                }

                if ($hora_inicio >= $hora_fin) {
                    Response::json(['error' => 'La hora de inicio debe ser menor que la hora de fin.'], 400);
                }
            }

            if (BloqueosRepository::existeSolape($fecha, $hora_inicio, $hora_fin, $id)) {
                Response::json(['error' => 'Ya existe otro bloqueo igual o solapado para esa fecha.'], 409);
            }

            $ok = BloqueosRepository::update(
                $id,
                $fecha,
                $hora_inicio,
                $hora_fin,
                $motivo
            );

            if (!$ok) {
                Response::json(['error' => 'No se pudo actualizar el bloqueo.'], 500);
            }

            Response::json(['message' => 'Bloqueo actualizado correctamente.']);
        } catch (Throwable $e) {
            Response::json(['error' => 'No se pudo actualizar el bloqueo.'], 500);
        }
    }

    public static function delete(int $id): void
    {
        try {
            AdminAuth::user();

            $bloqueo = BloqueosRepository::getById($id);

            if (!$bloqueo) {
                Response::json(['error' => 'El bloqueo no existe.'], 404);
            }

            $ok = BloqueosRepository::delete($id);

            if (!$ok) {
                Response::json(['error' => 'No se pudo eliminar el bloqueo.'], 500);
            }

            Response::json(['message' => 'Bloqueo eliminado correctamente.']);
        } catch (Throwable $e) {
            Response::json(['error' => 'No se pudo eliminar el bloqueo.'], 500);
        }
    }

    private static function validarFecha(string $fecha): bool
    {
        $obj = DateTime::createFromFormat('Y-m-d', $fecha);
        return $obj && $obj->format('Y-m-d') === $fecha;
    }

    private static function validarHora(string $hora): bool
    {
        $obj = DateTime::createFromFormat('H:i:s', $hora);
        return $obj && $obj->format('H:i:s') === $hora;
    }

    private static function normalizarHora(string $hora): string
    {
        if (preg_match('/^\d{2}:\d{2}$/', $hora)) {
            return $hora . ':00';
        }

        return $hora;
    }
}