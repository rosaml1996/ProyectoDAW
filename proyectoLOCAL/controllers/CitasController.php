<?php
require_once __DIR__ . '/../repositories/CitasRepository.php';
require_once __DIR__ . '/../repositories/ServiciosRepository.php';
require_once __DIR__ . '/../helpers/Response.php';
require_once __DIR__ . '/../helpers/Request.php';
require_once __DIR__ . '/../helpers/i18n.php';
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
                Response::json(['error' => t('missing_date_or_service')], 400);
            }

            if (!self::validarFecha($fecha)) {
                Response::json(['error' => t('invalid_date_format')], 400);
            }

            $servicio = ServiciosRepository::getById($id_servicio);

            if (!$servicio || (isset($servicio['activo']) && (int) $servicio['activo'] !== 1)) {
                Response::json(['error' => t('service_not_found_or_inactive')], 404);
            }

            $citas = CitasRepository::getDisponibles($fecha, $id_servicio);
            Response::json($citas);
        } catch (Throwable $e) {
            Response::json([
                'error' => t('load_available_slots_error'),
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
            Response::json(['error' => t('load_user_appointments_error')], 500);
        }
    }

    public static function getAll(): void
    {
        try {
            AdminAuth::user();
            $citas = CitasRepository::getAll();
            Response::json($citas);
        } catch (Throwable $e) {
            Response::json(['error' => t('load_appointments_error')], 500);
        }
    }

    public static function getById(int $id): void
    {
        try {
            $cita = CitasRepository::getById($id);

            if (!$cita) {
                Response::json(['error' => t('appointment_not_found')], 404);
            }

            Response::json($cita);
        } catch (Throwable $e) {
            Response::json(['error' => t('load_appointment_error')], 500);
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
                Response::json(['error' => t('missing_booking_data')], 400);
            }

            if (!self::validarFecha($fecha)) {
                Response::json(['error' => t('invalid_date_format')], 400);
            }

            if (!self::validarHora($hora_inicio)) {
                Response::json(['error' => t('invalid_time_format')], 400);
            }

            $fechaHoraReserva = strtotime($fecha . ' ' . $hora_inicio);
            if ($fechaHoraReserva < time()) {
                Response::json(['error' => t('cannot_book_past')], 400);
            }

            $servicio = ServiciosRepository::getById($id_servicio);

            if (!$servicio || (isset($servicio['activo']) && (int) $servicio['activo'] !== 1)) {
                Response::json(['error' => t('service_not_found_or_inactive')], 404);
            }

            $idCita = CitasRepository::reservar(
                $fecha,
                $hora_inicio,
                $id_servicio,
                (int) $usuario['id_paciente']
            );

            if (!$idCita) {
                Response::json(['error' => t('slot_not_available')], 409);
            }

            Response::json([
                'message' => t('appointment_booked_successfully'),
                'id_cita' => $idCita
            ]);
        } catch (Throwable $e) {
            Response::json([
                'error' => t('book_appointment_error'),
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
                Response::json(['error' => t('invalid_appointment')], 400);
            }

            $cita = CitasRepository::getById($id_cita);

            if (!$cita) {
                Response::json(['error' => t('appointment_not_found')], 404);
            }

            $ok = CitasRepository::anular($id_cita, (int) $usuario['id_paciente']);

            if (!$ok) {
                Response::json(['error' => t('cannot_cancel_appointment')], 409);
            }

            Response::json(['message' => t('appointment_cancelled_successfully')]);
        } catch (Throwable $e) {
            Response::json(['error' => t('cancel_appointment_error')], 500);
        }
    }

    public static function cancelarAdmin(): void
    {
        try {
            AdminAuth::user();

            $data = Request::json();
            $id_cita = filter_var($data['id_cita'] ?? null, FILTER_VALIDATE_INT);

            if (!$id_cita) {
                Response::json(['error' => t('invalid_appointment')], 400);
            }

            $cita = CitasRepository::getById($id_cita);

            if (!$cita) {
                Response::json(['error' => t('appointment_not_found')], 404);
            }

            $ok = CitasRepository::cancelarAdmin($id_cita);

            if (!$ok) {
                Response::json(['error' => t('cancel_appointment_error')], 409);
            }

            Response::json(['message' => t('appointment_cancelled_successfully')]);
        } catch (Throwable $e) {
            Response::json(['error' => t('cancel_appointment_error')], 500);
        }
    }

    public static function crear(): void
    {
        Response::json([
            'error' => t('manual_creation_disabled')
        ], 400);
    }

    public static function actualizar(int $id): void
    {
        Response::json([
            'error' => t('manual_edit_disabled')
        ], 400);
    }

    public static function eliminar(int $id): void
    {
        try {
            AdminAuth::user();

            $cita = CitasRepository::getById($id);

            if (!$cita) {
                Response::json(['error' => t('appointment_not_found')], 404);
            }

            $ok = CitasRepository::eliminar($id);

            if (!$ok) {
                Response::json(['error' => t('delete_appointment_error')], 500);
            }

            Response::json(['message' => t('appointment_deleted_successfully')]);
        } catch (Throwable $e) {
            Response::json(['error' => t('delete_appointment_error')], 500);
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