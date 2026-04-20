<?php
require_once __DIR__ . '/../repositories/CitasRepository.php';
require_once __DIR__ . '/../repositories/ServiciosRepository.php';
require_once __DIR__ . '/../helpers/Response.php';
require_once __DIR__ . '/../helpers/Request.php';
require_once __DIR__ . '/../security/Auth.php';
require_once __DIR__ . '/../security/AdminAuth.php';

class CitasController
{
    public static function disponibles(): void
    {
        try {
            $fecha = trim((string) ($_GET['fecha'] ?? ''));
            $id_servicio = filter_var($_GET['id_servicio'] ?? null, FILTER_VALIDATE_INT);

            if ($fecha === '' || !$id_servicio) {
                Response::json(['error' => 'Debes indicar fecha e id_servicio.'], 400);
            }

            if (!self::validarFecha($fecha)) {
                Response::json(['error' => 'La fecha debe tener formato YYYY-MM-DD.'], 400);
            }

            $servicio = ServiciosRepository::getById($id_servicio);

            if (!$servicio || (isset($servicio['activo']) && (int) $servicio['activo'] !== 1)) {
                Response::json(['error' => 'El servicio seleccionado no existe o no está activo.'], 404);
            }

            $citas = CitasRepository::getDisponibles($fecha, $id_servicio);
            Response::json($citas);
        } catch (Throwable $e) {
            Response::json([
                'error' => 'No se han podido cargar las horas disponibles.',
                'detalle' => $e->getMessage()
            ], 500);
        }
    }

    public static function mias(): void
    {
        try {
            $usuario = Auth::user();
            $citas = CitasRepository::getByPaciente((int) $usuario['id_paciente']);
            Response::json($citas);
        } catch (Throwable $e) {
            Response::json(['error' => 'No se pudieron cargar tus citas.'], 500);
        }
    }

    public static function getAll(): void
    {
        try {
            AdminAuth::user();
            $citas = CitasRepository::getAll();
            Response::json($citas);
        } catch (Throwable $e) {
            Response::json(['error' => 'No se pudieron cargar las citas.'], 500);
        }
    }

    public static function getById(int $id): void
    {
        try {
            $cita = CitasRepository::getById($id);

            if (!$cita) {
                Response::json(['error' => 'La cita no existe.'], 404);
            }

            Response::json($cita);
        } catch (Throwable $e) {
            Response::json(['error' => 'No se pudo obtener la información de la cita.'], 500);
        }
    }

    public static function reservar(): void
    {
        try {
            $usuario = Auth::user();
            $data = Request::json();

            $fecha = trim((string) ($data['fecha'] ?? ''));
            $hora_inicio = self::normalizarHora(trim((string) ($data['hora_inicio'] ?? '')));
            $id_servicio = filter_var($data['id_servicio'] ?? null, FILTER_VALIDATE_INT);

            if ($fecha === '' || $hora_inicio === '' || !$id_servicio) {
                Response::json(['error' => 'Debes indicar fecha, hora y servicio.'], 400);
            }

            if (!self::validarFecha($fecha)) {
                Response::json(['error' => 'La fecha debe tener formato YYYY-MM-DD.'], 400);
            }

            if (!self::validarHora($hora_inicio)) {
                Response::json(['error' => 'La hora debe tener formato HH:MM:SS.'], 400);
            }

            $fechaHoraReserva = strtotime($fecha . ' ' . $hora_inicio);
            if ($fechaHoraReserva < time()) {
                Response::json(['error' => 'No se puede reservar una cita en una fecha u hora pasada.'], 400);
            }

            $servicio = ServiciosRepository::getById($id_servicio);

            if (!$servicio || (isset($servicio['activo']) && (int) $servicio['activo'] !== 1)) {
                Response::json(['error' => 'El servicio seleccionado no existe o no está activo.'], 404);
            }

            $idCita = CitasRepository::reservar(
                $fecha,
                $hora_inicio,
                $id_servicio,
                (int) $usuario['id_paciente']
            );

            if (!$idCita) {
                Response::json(['error' => 'Ese horario ya no está disponible.'], 409);
            }

            Response::json([
                'message' => 'Cita reservada correctamente.',
                'id_cita' => $idCita
            ]);
        } catch (Throwable $e) {
            Response::json([
                'error' => 'No se pudo reservar la cita.',
                'detalle' => $e->getMessage()
            ], 500);
        }
    }

    public static function anular(): void
    {
        try {
            $usuario = Auth::user();
            $data = Request::json();

            $id_cita = filter_var($data['id_cita'] ?? null, FILTER_VALIDATE_INT);

            if (!$id_cita) {
                Response::json(['error' => 'La cita seleccionada no es válida.'], 400);
            }

            $cita = CitasRepository::getById($id_cita);

            if (!$cita) {
                Response::json(['error' => 'La cita que intentas anular no existe.'], 404);
            }

            $ok = CitasRepository::anular($id_cita, (int) $usuario['id_paciente']);

            if (!$ok) {
                Response::json(['error' => 'No puedes anular esa cita.'], 409);
            }

            Response::json(['message' => 'Cita anulada correctamente.']);
        } catch (Throwable $e) {
            Response::json(['error' => 'No se pudo anular la cita.'], 500);
        }
    }

    public static function cancelarAdmin(): void
    {
        try {
            AdminAuth::user();

            $data = Request::json();
            $id_cita = filter_var($data['id_cita'] ?? null, FILTER_VALIDATE_INT);

            if (!$id_cita) {
                Response::json(['error' => 'La cita seleccionada no es válida.'], 400);
            }

            $cita = CitasRepository::getById($id_cita);

            if (!$cita) {
                Response::json(['error' => 'La cita no existe.'], 404);
            }

            $ok = CitasRepository::cancelarAdmin($id_cita);

            if (!$ok) {
                Response::json(['error' => 'No se pudo cancelar la cita.'], 409);
            }

            Response::json(['message' => 'Cita cancelada correctamente.']);
        } catch (Throwable $e) {
            Response::json(['error' => 'No se pudo cancelar la cita.'], 500);
        }
    }

    public static function crear(): void
    {
        Response::json([
            'error' => 'La creación manual de citas libres ya no está disponible en el nuevo sistema.'
        ], 400);
    }

    public static function actualizar(int $id): void
    {
        Response::json([
            'error' => 'La edición manual de citas desde esta pantalla se revisará al adaptar el área de administración.'
        ], 400);
    }

    public static function eliminar(int $id): void
    {
        try {
            AdminAuth::user();

            $cita = CitasRepository::getById($id);

            if (!$cita) {
                Response::json(['error' => 'La cita que intentas eliminar no existe.'], 404);
            }

            $ok = CitasRepository::eliminar($id);

            if (!$ok) {
                Response::json(['error' => 'No se pudo eliminar la cita.'], 500);
            }

            Response::json(['message' => 'Cita eliminada correctamente.']);
        } catch (Throwable $e) {
            Response::json(['error' => 'No se pudo eliminar la cita.'], 500);
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