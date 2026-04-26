<?php
require_once __DIR__ . '/../repositories/BloqueosRepository.php';
require_once __DIR__ . '/../helpers/Response.php';
require_once __DIR__ . '/../helpers/Request.php';
require_once __DIR__ . '/../helpers/i18n.php';
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
            Response::json(['error' => t('blocks_load_error')], 500);
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
                Response::json(['error' => t('date_required')], 400);
            }

            if (!self::validarFecha($fecha)) {
                Response::json(['error' => t('date_format_invalid')], 400);
            }

            if ($fecha < date('Y-m-d')) {
                Response::json(['error' => t('block_past_date_error')], 400);
            }

            if ($motivo === '') {
                Response::json(['error' => t('block_reason_required')], 400);
            }

            $hora_inicio = $hora_inicio === '' ? null : self::normalizarHora($hora_inicio);
            $hora_fin = $hora_fin === '' ? null : self::normalizarHora($hora_fin);

            $esDiaCompleto = ($hora_inicio === null && $hora_fin === null);
            $esParcialValido = ($hora_inicio !== null && $hora_fin !== null);

            if (!$esDiaCompleto && !$esParcialValido) {
                Response::json(['error' => t('block_hours_required')], 400);
            }

            if ($esParcialValido) {
                if (!self::validarHora($hora_inicio) || !self::validarHora($hora_fin)) {
                    Response::json(['error' => t('time_format_invalid')], 400);
                }

                if ($hora_inicio >= $hora_fin) {
                    Response::json(['error' => t('start_time_before_end_time')], 400);
                }
            }

            if (BloqueosRepository::existeSolape($fecha, $hora_inicio, $hora_fin)) {
                Response::json(['error' => t('block_overlap_error')], 409);
            }

            $id = BloqueosRepository::create($fecha, $hora_inicio, $hora_fin, $motivo);

            if (!$id) {
                Response::json(['error' => t('block_save_error')], 500);
            }

            Response::json([
                'message' => t('block_saved_success'),
                'id_bloqueo' => $id
            ], 201);
        } catch (Throwable $e) {
            Response::json(['error' => t('block_save_error')], 500);
        }
    }

    public static function update(int $id): void
    {
        try {
            AdminAuth::user();

            $bloqueo = BloqueosRepository::getById($id);

            if (!$bloqueo) {
                Response::json(['error' => t('block_not_found')], 404);
            }

            $data = Request::json();

            $fecha = trim((string) ($data['fecha'] ?? ''));
            $hora_inicio = trim((string) ($data['hora_inicio'] ?? ''));
            $hora_fin = trim((string) ($data['hora_fin'] ?? ''));
            $motivo = trim((string) ($data['motivo'] ?? ''));

            if ($fecha === '') {
                Response::json(['error' => t('date_required')], 400);
            }

            if (!self::validarFecha($fecha)) {
                Response::json(['error' => t('date_format_invalid')], 400);
            }

            if ($fecha < date('Y-m-d')) {
                Response::json(['error' => t('block_update_past_date_error')], 400);
            }

            if ($motivo === '') {
                Response::json(['error' => t('block_reason_required')], 400);
            }

            $hora_inicio = $hora_inicio === '' ? null : self::normalizarHora($hora_inicio);
            $hora_fin = $hora_fin === '' ? null : self::normalizarHora($hora_fin);

            $esDiaCompleto = ($hora_inicio === null && $hora_fin === null);
            $esParcialValido = ($hora_inicio !== null && $hora_fin !== null);

            if (!$esDiaCompleto && !$esParcialValido) {
                Response::json(['error' => t('block_hours_required')], 400);
            }

            if ($esParcialValido) {
                if (!self::validarHora($hora_inicio) || !self::validarHora($hora_fin)) {
                    Response::json(['error' => t('time_format_invalid')], 400);
                }

                if ($hora_inicio >= $hora_fin) {
                    Response::json(['error' => t('start_time_before_end_time')], 400);
                }
            }

            if (BloqueosRepository::existeSolape($fecha, $hora_inicio, $hora_fin, $id)) {
                Response::json(['error' => t('block_overlap_other_error')], 409);
            }

            $ok = BloqueosRepository::update(
                $id,
                $fecha,
                $hora_inicio,
                $hora_fin,
                $motivo
            );

            if (!$ok) {
                Response::json(['error' => t('block_update_error')], 500);
            }

            Response::json(['message' => t('block_updated_success')]);
        } catch (Throwable $e) {
            Response::json(['error' => t('block_update_error')], 500);
        }
    }

    public static function delete(int $id): void
    {
        try {
            AdminAuth::user();

            $bloqueo = BloqueosRepository::getById($id);

            if (!$bloqueo) {
                Response::json(['error' => t('block_not_found')], 404);
            }

            $ok = BloqueosRepository::delete($id);

            if (!$ok) {
                Response::json(['error' => t('block_delete_error')], 500);
            }

            Response::json(['message' => t('block_deleted_success')]);
        } catch (Throwable $e) {
            Response::json(['error' => t('block_delete_error')], 500);
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